<div class="%day%-slot">
    <div class="slot-top">
        <label class="day-text control-label">{MEND_SIGN}%day_title%: &nbsp;</label> 
        <div class="form-check">
            <input type="checkbox" name="oh[%day%][status][]" class="dayofweek day-toggle form-check-input" data-day="%day%" value="n" id="d1" />
            <label class="form-check-label" for="d1">Mark As Unavailable</label>
        </div>
    </div>
    
    <div class="col-lg-10 row">
        <div class="col-4 pl-1 pr-1 time-fields %day%" style="padding-bottom: 8px;">
            <input type="text" class="clockpicker from-time form-control required" id="%day%_from_%index%" name="oh[%day%][from_time][]" placeholder="From Time*" data-error-container="#error_%day%_from_%index%" readonly>
            <div class="error_%day%_from_%index%" id="error_%day%_from_%index%"></div>
        </div>

        <div class="col-4 pl-1 pr-1 time-fields %day%" style="padding-bottom: 8px;">
            <input type="text" class="clockpicker to-time form-control required" id="%day%_to_%index%" name="oh[%day%][to_time][]" placeholder="To Time*" data-error-container="#error_%day%_to_%index%" readonly>
            <div class="error_%day%_to_%index%" id="error_%day%_to_%index%"></div>
        </div>
        <div class="col-1 d-flex align-items-center time-fields %day%">
            <button type="button" class="btn btn-sm btn-success add-slot" data-type="%day%">
                +
            </button>
        </div>
    </div>
    
</div>