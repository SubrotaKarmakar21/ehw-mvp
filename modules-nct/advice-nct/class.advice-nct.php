<?php

class Advice {

    public function __construct($module = "", $reqData = array()){
        foreach ($GLOBALS as $key => $values) {
            $this->$key = $values;
        }

        $this->module = $module;
        $this->reqData = $reqData;
    }

    public function getPageContent(){

        global $db, $sessUserId;

        if(empty($sessUserId)){
                header("Location: ".SITE_URL."login");
                exit;
        }

        $user = $db->pdoQuery("
                SELECT user_type, parent_id FROM tbl_users
                WHERE id = ".$sessUserId."
        ")->result();

        if(empty($user) || $user['user_type'] != 'doctor'){
                header("Location:".SITE_URL."dashboard");
                exit;
        }

        $prescription_id = isset($_GET['prescription_id']) ? (int)$_GET['prescription_id'] : 0;

        if($prescription_id <= 0){
                return "Invalid Prescription";
        }

        // Fetch required fields
        $prescription = $db->pdoQuery("
                SELECT complaints_json, diagnosis, medications_json, investigations_json, general_advice, diet_plan
                FROM tbl_prescriptions
                WHERE id = ".$prescription_id."
                AND doctor_id = ".$sessUserId."
                LIMIT 1
        ")->result();

        if(empty($prescription)){
                return "Prescription not found";
        }

        // AI GENERATED GENERAL ADVICE
        if(!empty($prescription['general_advice'])){
                $generalAdvice = $prescription['general_advice'];
        } else {
                //Decode JSON
                $complaints = json_decode($prescription['complaints_json'], true) ?: [];
                $medications = json_decode($prescription['medications_json'], true) ?: [];
                $investigations = json_decode($prescription['investigations_json'], true) ?: [];

                $diagnosis = trim($prescription['diagnosis'] ?? '');

                $complaintsText = !empty($complaints) ? implode(", ", $complaints) : "None";

                $medNames = [];
                foreach($medications as $m){
                        if(!empty($m['medicine'])){
                                $medNames[] = $m['medicine'];
                        }
                }
                $medicationsText = !empty($medNames) ? implode(", ", $medNames) : "None";

                $investigationsText = !empty($investigations) ? implode(", ", $investigations) : "None";

                // Call AI
                $generalAdvice = $this->generateAdvice($complaintsText,$diagnosis,$medicationsText,$investigationsText);

                // Save in DB
                $db->update("tbl_prescriptions", ["general_advice" => $generalAdvice], ["id" => $prescription_id]);
        }

        // AI GENERATED DIET PLAN
        if(!empty($prescription['diet_plan'])){
                $dietPlan = $prescription['diet_plan'];
        } else {

                // reusing already decoded data
                $dietPlan = $this->generateDietPlan($complaintsText,$diagnosis,$medicationsText);

                // Save in DB
                $db->update("tbl_prescriptions", ["diet_plan" => $dietPlan], ["id" => $prescription_id]);
        }

        // AI GENERATED DOs AND DONTs
        if(!empty($prescription['dos_donts_json'])){
                $dosDonts = json_decode($prescription['dos_donts_json'], true);
        } else {

                $json = $this->generateDosDonts($complaintsText, $diagnosis, $medicationsText);

                $dosDonts = json_decode($json, true);

                // fallback safety
                if(empty($dosDonts['dos']) || empty($dosDonts['donts'])){
                        $dosDonts = ["dos" => ["Stay hydrated", "Take rest", "Eat light meals"],"donts" => ["Avoid junk food", "Avoid heavy meals", "Avoid dehydration"]];
                }

                $db->update("tbl_prescriptions", ["dos_donts_json" => json_encode($dosDonts)], ["id" => $prescription_id]);
        }

        $rows = "";
        $count = min(count($dosDonts['dos']), count($dosDonts['donts']));

        for($i = 0; $i < $count; $i++){
                $rows .= "
                        <tr>
                                <td>• ".$dosDonts['dos'][$i]."</td>
                                <td>• ".$dosDonts['donts'][$i]."</td>
                        </tr>";
        }

        $replace = array(
                "%PRESCRIPTION_ID%"     => $prescription_id,
                "%GENERAL_ADVICE%"      => $generalAdvice,
                "%DIET_PLAN%"           => $dietPlan,
                "%DOS_DONTS_TABLE%"     => $rows
        );

        $filePath = DIR_TMPL.$this->module."/".$this->module.".tpl.php";

        $tpl = new MainTemplater($filePath);
        $tpl = $tpl->parse();

        return str_replace(array_keys($replace), array_values($replace), $tpl);
    }

    private function generateAdvice($complaints, $diagnosis, $medications, $investigations){

        $apiKey = OPENAI_API_KEY;

        $prompt = "
                You are a medical assistant helping a doctor.

                Generate short, safe, general advice for a patient.

                STRICT RULES:
                - Do NOT mention diagnosis
                - Do NOT give medical conclusions
                - Keep it general
                - Max 4-5 bullet points
                - Use bullet points (•)
                - Each point should be on a new line
                - Do NOT leave empty lines between points
                - Simple language

                Symptoms: $complaints
                Medications: $medications
                Tests: $investigations
        ";

        $data = [
                "model" => "gpt-4o-mini",
                "messages" => [
                        ["role" => "system", "content" => "You are a helpful medical assistant."],
                        ["role" => "user", "content" => $prompt]
                ],
                "temperature" => 0.4
        ];

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, "https://api.openai.com/v1/chat/completions");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);

        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json","Authorization: Bearer " . $apiKey]);

        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        $response = curl_exec($ch);

        if(curl_errno($ch)){
                return "• Unable to generate advice";
        }

        curl_close($ch);

        $result = json_decode($response, true);

        return $result['choices'][0]['message']['content'] ?? "• Unable to generate advice";
    }

    private function generateDietPlan($complaints, $diagnosis, $medications){

        $apiKey = OPENAI_API_KEY;

        $prompt = "
                You are a medical assistant helping a doctor.

                Generate a simple and safe general diet suggestion.

                STRICT RULES:
                - Do NOT mention diagnosis
                - Do NOT give strict diet plans
                - Keep it general and safe
                - Max 4-5 points
                - Use bullet points (•)
                - Do NOT leave emplty lines between points
                - Each point on new line
                - Simple language

                Symptoms: $complaints
                Medications: $medications
        ";

        $data = [
                "model" => "gpt-4o-mini",
                "messages" => [
                        ["role" => "system", "content" => "You are a helpful medical assistant."],
                        ["role" => "user", "content" => $prompt]
                ],
                "temperature" => 0.4
        ];

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, "https://api.openai.com/v1/chat/completions");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);

        curl_setopt($ch, CURLOPT_HTTPHEADER, [
                "Content-Type: application/json",
                "Authorization: Bearer " . $apiKey
        ]);

        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        $response = curl_exec($ch);

        if(curl_errno($ch)){
                return "• Follow light and balanced meals\n• Stay hydrated";
        }

        curl_close($ch);

        $result = json_decode($response, true);

        return $result['choices'][0]['message']['content'] ?? "• Follow light and balanced meals";
    }

    private function generateDosDonts($complaints, $diagnosis, $medications){

        $apiKey = OPENAI_API_KEY;

        $prompt = "
                You are a medical assistant helping a doctor.

                Generate DOs and DON'Ts for a patient.

                STRICT RULES:
                - Keep points short (max 5-6 words)
                - Equal number of DOs and DON'Ts (3 to 5 each)
                - No explanations
                - No diagnosis mention
                - Simple language

                Return ONLY JSON in this format:
                {\"dos\": [\"...\"],\"donts\": [\"...\"]}

                Symptoms: $complaints
                Medications: $medications
                ";

        $data = [
                "model" => "gpt-4o-mini",
                "messages" => [
                        ["role" => "system", "content" => "You are a strict JSON generator."],
                        ["role" => "user", "content" => $prompt]
                ],
                "temperature" => 0.3
        ];

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, "https://api.openai.com/v1/chat/completions");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);

        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json","Authorization: Bearer " . $apiKey]);

        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        $response = curl_exec($ch);
        curl_close($ch);

        $result = json_decode($response, true);

        $content = $result['choices'][0]['message']['content'] ?? "{}";

        return $content;
    }
}
