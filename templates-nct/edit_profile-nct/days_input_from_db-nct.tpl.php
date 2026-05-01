<div class="%day%-slot slot_container_%id%">
    <div class="slot-top %hide_if_not_first%">
        <label class="day-text form-label control-label">%label_title% &nbsp;</label>
        <div class="mark_as_checkbox">
            %check_box_html%
        </div>
    </div>

    <div class="row g-2 align-items-center">
        <div class="col-md-5  time-fields %day% %hide_if_unavailable%">
            <input type="text" class="clockpicker from-time form-control %required%" id="%day%_from_%index%" name="%day%_from_%index%" placeholder="From Time*" value="%day_from_time%" data-error-container="#error_%day%_from_%index%" readonly>
            <div class="error_%day%_from_%index%" id="error_%day%_from_%index%"></div>
        </div>
        <div class="col-md-5 time-fields %day% %hide_if_unavailable%" style="padding-bottom: 8px;">
            <input type="text" class="clockpicker to-time form-control %required%" id="%day%_to_%index%" name="%day%_to_%index%" placeholder="To Time*" value="%day_to_time%" data-error-container="#error_%day%_to_%index%" readonly>
            <div class="error_%day%_to_%index%" id="error_%day%_to_%index%"></div>
        </div>

        %add_remove_icon%
        <input type="hidden" class="main_update_id" name="db_value_id_%day%_to_%index%" id="%day%_id" value="%id%">

        <input type="hidden" name="seasonal_price_action%temp%[]" value="%day%_%index%">
    </div>
</div>