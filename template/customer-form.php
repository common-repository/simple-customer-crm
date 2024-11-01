<form class="form-horizontal" id="customer-crm-form">
    <fieldset>
        <!-- Form Name -->
        <legend><?php esc_html_e($form_title,'simple_customer_crm'); ?></legend>
        <!-- Text input-->
        <div class="col-md-12 hidden-error-msg">
            <input id="sccrm_customer_create_date" name="sccrm_customer_create_date" type="hidden" required="" value="<?php esc_attr_e($current_date_time,'simple_customer_crm'); ?>">
        </div>
        <div class="form-group">
            <label class="col-md-4 control-label" for="sccrm_customer_name"><?php esc_html_e($name_field_label,'simple_customer_crm'); ?></label>  
            <div class="col-md-8">
                <input id="sccrm_customer_name" name="sccrm_customer_name" type="text" placeholder="<?php esc_attr_e('Enter Your Name','simple_customer_crm'); ?>" class="form-control input-md" required="" minlength="<?php esc_attr_e($name_field_min_length,'simple_customer_crm');?>" maxlength="<?php esc_attr_e($name_field_max_length,'simple_customer_crm');?>" >
            </div>
        </div>
        <!-- Text input-->
        <div class="form-group">
            <label class="col-md-4 control-label" for="sccrm_customer_phone"><?php esc_html_e($phone_field_label,'simple_customer_crm'); ?></label>  
            <div class="col-md-8">
                <input id="sccrm_customer_phone" name="sccrm_customer_phone" type="text" placeholder="<?php esc_attr_e('Enter Your Phone No.','simple_customer_crm'); ?>" class="form-control input-md" required="" minlength="<?php esc_attr_e($phone_field_min_length,'simple_customer_crm');?>" maxlength="<?php esc_attr_e($phone_field_max_length,'simple_customer_crm');?>">
            </div>
        </div>
        <!-- Text input-->
        <div class="form-group">
            <label class="col-md-4 control-label" for="sccrm_customer_email"><?php esc_html_e($email_field_label,'simple_customer_crm'); ?></label>  
            <div class="col-md-8">
                <input id="sccrm_customer_email" name="sccrm_customer_email" type="email" placeholder="<?php esc_attr_e('Enter Your Email','simple_customer_crm'); ?>" class="form-control input-md" required="">
            </div>
        </div>
        <!-- Text input-->
        <div class="form-group">
            <label class="col-md-4 control-label" for="sccrm_customer_budget"><?php esc_html_e($budget_field_label,'simple_customer_crm'); ?></label>            
            <div class="col-md-2">
                <select id="sccrm_customer_currency" name="sccrm_customer_currency" class="form-control">
                    <option value="usd"><?php esc_html_e('USD','simple_customer_crm'); ?> &dollar;</option>
                    <option value="euro"><?php esc_html_e('Euro','simple_customer_crm'); ?> &euro;</option>
                </select>            
            </div>
            <div class="col-md-6">
                <input id="sccrm_customer_budget" name="sccrm_customer_budget" type="number" placeholder="<?php esc_attr_e('Enter Your Budget.','simple_customer_crm'); ?>" class="form-control input-md" required="" >
            </div>
        </div>
        <!-- Textarea -->
        <div class="form-group">
            <label class="col-md-4 control-label" for="sccrm_customer_message"><?php esc_html_e($message_field_label,'simple_customer_crm'); ?></label>
            <div class="col-md-8">                     
                <textarea class="form-control input-md " id="sccrm_customer_message" name="sccrm_customer_message" placeholder="<?php esc_attr_e('Enter Your Message.','simple_customer_crm'); ?>" rows="<?php esc_attr_e($message_field_height,'simple_customer_crm');?>" cols="<?php esc_attr_e($message_field_width,'simple_customer_crm');?>" ></textarea>
            </div>
        </div>
        <!-- Button -->
        <div class="form-group">
            <label class="col-md-4 control-label" for=""></label>
            <div class="col-md-4">
                <button id="customer-form-submit" name="customer-form-submit" class="btn btn-primary"><?php esc_html_e($submit_field_label,'simple_customer_crm'); ?></button>
                <img class="ajax-loader hide" src="<?php echo SIMPLE_CUSTOMER_CRM_PLUGIN_IMAGE_URI; ?>ajax-loading.gif">
            </div>
        </div>
        <div class="form-group">
            <div class="sccrm_msg_container text-center">
                <span id="sccrm_msg" class="sccrm_msg_class"></span>
            </div>
        </div>
    </fieldset>
</form>
