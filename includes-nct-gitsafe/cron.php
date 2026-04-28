<?php
require_once 'config-nct.php';
/*
 * Time blocks 24/3
 * Cron to be run on below timings :
 * 8 AM, 4PM, 12AM
 */
$response_time = (int)ADMIN_RESPONSE_TIME;
//change status of project whose action time has passed the imposed time
/*
 * Expiry
 * if no bid received within "15" days of project post then "expired"
 */
$affectedRows1 = $db->pdoQuery('UPDATE tbl_projects SET jobStatus = "expired" WHERE id NOT IN (SELECT DISTINCT projectId FROM tbl_bids) AND TIMESTAMPDIFF(HOUR, createdDate,NOW()) >= '.$response_time.' AND jobStatus = "open"')->affectedRows();
$fb->info($affectedRows1,'if no bid received within "15" days of project post then "expired"');
/*
 * Reopen
 * if provider does not send milestones within ADMIN_RESPONSE_TIME then "Reopen"
 */
$affectedRows2 = $db->pdoQuery('UPDATE tbl_projects SET jobStatus = "reopen", isFeatured="n" WHERE id IN (SELECT DISTINCT projectId FROM tbl_bids WHERE isAccepted = 1 AND TIMESTAMPDIFF(HOUR, updateTime, NOW()) >= '.$response_time.' ) AND jobStatus = "accepted" ')->affectedRows();
$fb->info($affectedRows2,'if provider does not send milestones within ADMIN_RESPONSE_TIME then "Reopen"');
/*
 * Reopen
 * if customer does not pay first milestone within ADMIN_RESPONSE_TIME of the one he accepted then "Reopen"
 */
$affectedRows3 = $db->pdoQuery('UPDATE tbl_projects SET jobStatus = "reopen", isFeatured="n" WHERE id IN (SELECT DISTINCT projectId FROM tbl_milestone WHERE STATUS <> "paid" AND TIMESTAMPDIFF(HOUR, created_date, NOW()) >= '.$response_time.') AND jobStatus = "milestone_accepted" ')->affectedRows();
$fb->info($affectedRows3,'if customer does not pay first milestone within ADMIN_RESPONSE_TIME of the one he accepted then "Reopen"');
//send noti for projects those status is to be changed in next time block
/*
 * send alert approx. 12hrs before Expiry
 */
$allToBeExpired = $db->pdoQuery('SELECT p.id, p.slug, p.title, u.userId, u.profileLink, u.firstName, CONCAT_WS(" ",u.firstName,u.`lastName`) AS fullName, u.email FROM tbl_projects AS p LEFT JOIN tbl_users AS u ON p.`userId` = u.`userId` WHERE p.id NOT IN (SELECT DISTINCT projectId FROM tbl_bids) AND TIMESTAMPDIFF(HOUR, p.createdDate, NOW()) >= '.$response_time.' AND p.jobStatus = "open" ')->results();
$fb->info($allToBeExpired,'send alert approx. 12hrs before Expiry');
if (!empty($allToBeExpired)) {
	foreach ($allToBeExpired as $key => $value) {
		extract($value);
		// send mail & notification
		$array = generateEmailTemplate('cron_toBeExpired', array(
			'greetings' => ucfirst($firstName),
			'projectName' => ucwords($title),
			'projectLink' => SITE_URL . $profileLink . '/' . $slug,
			'date' => date('d M, Y')
		));
		//echo '<br/>'.$array['message'];exit;
		sendEmailAddress($email, $array['subject'], $array['message']);

		//send mobile notification
		pushToAndroid(array(
                        getTableValue("tbl_users", "deviceId", array("userId" => getTableValue("tbl_projects","userId",array("slug"=>$slug)))),
                    ), array(
                        'title' => 'You have receieved a new notification',
                        'body'  => getTableValue("tbl_email_templates","subject",array("constant"=>'cron_toBeExpired')),
                    ));

		//send notification
		//insert_user_notification($typeId = 5, $to = $this->proj['provider']['userId'], $referenceId = $this->proj['id']);

	}

}
/*
 * send alert approx. 12hrs before Reopen
 * if provider does not send milestones within ADMIN_RESPONSE_TIME then "Reopen"
 */
$allToBeExpired = $db->pdoQuery('SELECT p.id, p.slug, p.title, u.userId, u.profileLink, u.firstName, CONCAT_WS(" ",u.firstName,u.`lastName`) AS fullName, u.email FROM tbl_projects AS p LEFT JOIN tbl_users AS u ON p.`userId` = u.`userId` WHERE p.id NOT IN (SELECT DISTINCT projectId FROM tbl_bids) AND TIMESTAMPDIFF(HOUR, p.createdDate, NOW()) >= '.$response_time.' AND p.jobStatus = "open" ')->results();
$fb->info($allToBeExpired,'if provider does not send milestones within ADMIN_RESPONSE_TIME then "Reopen"');
if (!empty($allToBeExpired)) {
	foreach ($allToBeExpired as $key => $value) {
		extract($value);
		// send mail & notification
		$array = generateEmailTemplate('cron_toBeReopened_ProviderNotSendMil', array(
			'greetings' => ucfirst($firstName),
			'projectName' => ucwords($title),
			'projectLink' => SITE_URL . $profileLink . '/' . $slug,
			'date' => date('d M, Y')
		));
		//echo '<br/>'.$array['message'];exit;
		sendEmailAddress($email, $array['subject'], $array['message']);


		//send mobile notification
		pushToAndroid(array(
                        getTableValue("tbl_users", "deviceId", array("userId" => getTableValue("tbl_projects","userId",array("slug"=>$slug)))),
                    ), array(
                        'title' => 'You have receieved a new notification',
                        'body'  => getTableValue("tbl_email_templates","subject",array("constant"=>'cron_toBeReopened_ProviderNotSendMil')),
                    ));

		//send notification
		//insert_user_notification($typeId = 5, $to = $this->proj['provider']['userId'], $referenceId = $this->proj['id']);
	}

}
/*
 * send alert approx. 12hrs before Reopen
 * if customer does not pay first milestone within ADMIN_RESPONSE_TIME of the one he accepted then "Reopen"
 */
$allToBeExpired = $db->pdoQuery(' SELECT p.id, p.slug, p.title, u.userId, u.profileLink, u.firstName, CONCAT_WS(" ", u.firstName, u.`lastName`) AS fullName, u.email FROM tbl_projects AS p LEFT JOIN tbl_users AS u ON p.`userId` = u.`userId` WHERE p.id IN (SELECT DISTINCT projectId FROM tbl_milestone WHERE projectId NOT IN (SELECT DISTINCT m.projectId FROM tbl_milestone AS m WHERE m.`status` = "paid")) AND p.jobStatus = "milestone_accepted"  ')->results();
$fb->info($allToBeExpired,'if customer does not pay first milestone within ADMIN_RESPONSE_TIME of the one he accepted then "Reopen"');
if (!empty($allToBeExpired)) {
	foreach ($allToBeExpired as $key => $value) {
		extract($value);
		// send mail & notification
		$array = generateEmailTemplate('cron_toBeReopened_CustomerNotPayFirstMil', array(
			'greetings' => ucfirst($firstName),
			'projectName' => ucwords($title),
			'projectLink' => SITE_URL . $profileLink . '/' . $slug,
			'date' => date('d M, Y')
		));
		//echo '<br/>'.$array['message'];exit;
		sendEmailAddress($email, $array['subject'], $array['message']);

		//send mobile notification
		pushToAndroid(array(
                        getTableValue("tbl_users", "deviceId", array("userId" => getTableValue("tbl_projects","userId",array("slug"=>$slug)))),
                    ), array(
                        'title' => 'You have receieved a new notification',
                        'body'  => getTableValue("tbl_email_templates","subject",array("constant"=>'cron_toBeReopened_CustomerNotPayFirstMil')),
                    ));

		//send notification
		//insert_user_notification($typeId = 5, $to = $this->proj['provider']['userId'], $referenceId = $this->proj['id']);
	}

}


/*
 * START:: remove from featured after expiry date or not in open, reopened
 */

$allFeatured = $db->pdoQuery(' SELECT p.id FROM tbl_projects AS p WHERE p.featuredExpiryDate > NOW() OR p.`featuredExpiryDate` IS NULL OR p.`jobStatus` NOT IN ("open", "reopened") ')->results();
//$fb->info($allFeatured,'remove from featured after expiry date or not in open, reopened');
if (!empty($allFeatured)) {
	foreach ($allFeatured as $key => $value) {		
		$db->update('tbl_projects', array('isFeatured'=>'n','featuredExpiryDate'=>''), array('id'=>$value));		
	}
}
/*
 * END:: remove from featured after expiry date
 */



/*
 * START:: unlink files not required any more
 */

deleteUserFiles();
deleteSkillImages();
deleteHomeBanner();
deleteProjectFiles();
deleteMsgAttachments();

/*
 * END:: unlink files not required any more
 */

/*
 * START:: create lang file again if admin forgot to do so
 */
makeConstantFile();
/*
 * END:: create lang file again if admin forgot to do so
 */

?>