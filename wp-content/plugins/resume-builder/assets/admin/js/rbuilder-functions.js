var $_ResumeBuilderConditionalTimeout  = false;
var textareaID;

(function( $ ) {

    $(document).ready(function() {

	    var $_ResumeBuilderColorPickers 		= $('.rbuilder-color-field'),
	    	$_ResumeBuilderSelectFields 		= $('#resume_builder_settings').find('select'),
			$_ResumeBuilderTabs 				= $('#rbuilder-resume-tabs'),
			$_ResumeBuilderSettingsTabs			= $('#rbuilder-settings-tabs'),
			$_ResumeBuilderSettings 			= $('#resume_builder_settings'),
	    	$_ResumeBuilderSortable				= $('.rbuilder-sortable'),
			$_ResumeBuilderShortcodeField 		= $('.rbuilder-shortcode-field'),
			$_ResumeBuilderExperienceBuilder	= $('#rbuilder-experience-builder'),
			$_ResumeBuilderSkillsBuilder		= $('#rbuilder-skills-builder');

	    // Resume Builder Color Pickers
	    if ($_ResumeBuilderColorPickers.length){
		    $_ResumeBuilderColorPickers.wpColorPicker();
	    }

	    // Resume Builder Sortables
	    if ($_ResumeBuilderSortable.length){
		    $_ResumeBuilderSortable.sortable({
		    	handle: '.fa-bars',
		    	start: function(event, ui) {
			        if ( $(ui.item).find('.wp-editor-container textarea').length > 0 ){
			        	textareaID = $(ui.item).find('.wp-editor-container textarea').attr('id');
			        } else {
			        	textareaID = $(ui.item).find('.mce-container').parent().find( 'textarea' ).attr('id');
			        }
        			try { tinyMCE.execCommand('mceRemoveEditor', false, textareaID); } catch(e){}
			    },
			    stop: function(event, ui) {
			        try { tinyMCE.execCommand('mceAddEditor', false, textareaID); } catch(e){}
        			$(this).find('.update-warning').show();
			    }
		    });
	    }

	    // Resume Builder Select Wrappers
	    if ($_ResumeBuilderSelectFields.length){
		    $_ResumeBuilderSelectFields.each(function(){
			   	$(this).wrap('<div class="rbuilder-select-wrapper" />');
		    });
	    }

        // Resume Builder Shortcode Fields
	    if ($_ResumeBuilderShortcodeField.length){
		    $_ResumeBuilderShortcodeField.on('click',function(e){
				$(this).select();
			});
	    }

		// Builder Tabs
		if ($_ResumeBuilderTabs.length){

			var $_ResumeBuilderTab 			= $_ResumeBuilderTabs.find('li'),
				$_ResumeBuilderTabsOffset 	= $_ResumeBuilderTabs.offset().top - 32; // 32px for the admin bar

			$(window).scroll(function() {
			    var scroll = $(window).scrollTop();
			    if (scroll >= $_ResumeBuilderTabsOffset) {
			        $_ResumeBuilderSettings.addClass("stuck");
			    } else {
				    $_ResumeBuilderSettings.removeClass("stuck");
			    }
			});

			$_ResumeBuilderTab.on('click',function(e){

				e.preventDefault();
				window.scrollTo(0,0);

				var thisTab 	= $(this),
					thisTabID 	= thisTab.attr('id');

				thisTabID = thisTabID.split('rbuilder-resume-tab-');
				thisTabID = thisTabID[1];
				$('.rbuilder-resume-tab-content').hide();
				$('#rbuilder-resume-tab-content-'+thisTabID).show();

				$_ResumeBuilderTab.removeClass('active');
				thisTab.addClass('active');

			});
		}

		// Settings Tabs
		if ($_ResumeBuilderSettingsTabs.length){

			var $_ResumeBuilderSettingsTab = $_ResumeBuilderSettingsTabs.find('li');

			$_ResumeBuilderSettingsTab.on('click',function(e){

				e.preventDefault();
				window.scrollTo(0,0);

				var thisTab 	= $(this),
					thisTabID 	= thisTab.attr('id');

				thisTabID = thisTabID.split('rbuilder-settings-tab-');
				thisTabID = thisTabID[1];
				$('.rbuilder-settings-tab-content').hide();
				$('#rbuilder-settings-tab-content-'+thisTabID).show();

				$_ResumeBuilderSettingsTab.removeClass('active');
				thisTab.addClass('active');

			});
		}

		if ($_ResumeBuilderExperienceBuilder.length){

			rbuilder_reset_experience_builder();

			$_ResumeBuilderExperienceBuilder.parent().on('click','.rbuilder-add-experience-button',function(e){
				e.preventDefault();
				var clonedExperienceTemplate = $_ResumeBuilderExperienceBuilder.parent().find('.rbuilder-experience-template').clone().removeClass('rbuilder-template rbuilder-experience-template').addClass('rbuilder-experience-block rbuilder-experience-large');
				$_ResumeBuilderExperienceBuilder.append(clonedExperienceTemplate);
				rbuilder_reset_experience_builder();
			});

			$_ResumeBuilderExperienceBuilder.parent().on('click','.rbuilder-add-heading-button',function(e){
				e.preventDefault();
				var clonedHeadingTemplate = $_ResumeBuilderExperienceBuilder.parent().find('.rbuilder-heading-template').clone().removeClass('rbuilder-template rbuilder-heading-template').addClass('rbuilder-experience-block rbuilder-experience-heading');
				$_ResumeBuilderExperienceBuilder.append(clonedHeadingTemplate);
				rbuilder_reset_experience_builder();
			});

			$_ResumeBuilderExperienceBuilder.parent().on('click','.rbuilder-add-text-button',function(e){
				e.preventDefault();
				var clonedTextTemplate = $_ResumeBuilderExperienceBuilder.parent().find('.rbuilder-text-template').clone().removeClass('rbuilder-template rbuilder-text-template').addClass('rbuilder-experience-block rbuilder-experience-medium rbuilder-experience-text-content');
				$_ResumeBuilderExperienceBuilder.append(clonedTextTemplate);
				rbuilder_reset_experience_builder();
			});

			$_ResumeBuilderExperienceBuilder.parent().on('click','.rbuilder-delete-experience',function(e){
				e.preventDefault();
				$(this).parent().remove();
				rbuilder_reset_experience_builder();
			});

		}

		if ($_ResumeBuilderSkillsBuilder.length){

			rbuilder_reset_skills_builder();

			$_ResumeBuilderSkillsBuilder.parent().on('click','.rbuilder-add-skills-button',function(e){
				e.preventDefault();
				var clonedSkillTemplate = $_ResumeBuilderSkillsBuilder.parent().find('.rbuilder-skills-template').clone().removeClass('rbuilder-template rbuilder-skills-template').addClass('rbuilder-skills-block');
				$_ResumeBuilderSkillsBuilder.append(clonedSkillTemplate);
				rbuilder_reset_skills_builder();
			});

			$_ResumeBuilderSkillsBuilder.parent().on('click','.rbuilder-add-heading-button',function(e){
				e.preventDefault();
				var clonedHeadingTemplate = $_ResumeBuilderSkillsBuilder.parent().find('.rbuilder-heading-template').clone().removeClass('rbuilder-template rbuilder-heading-template').addClass('rbuilder-skills-block rbuilder-skills-heading');
				$_ResumeBuilderSkillsBuilder.append(clonedHeadingTemplate);
				rbuilder_reset_skills_builder();
			});

			$_ResumeBuilderSkillsBuilder.parent().on('click','.rbuilder-delete-skills',function(e){
				e.preventDefault();
				$(this).parent().remove();
				rbuilder_reset_skills_builder();
			});

		}

	});

})( jQuery );

// Reset Experience Builder
function rbuilder_reset_experience_builder(){

	var experienceBlocks = jQuery('.rbuilder-experience-block');
	experienceBlocks.each(function(){

		var randomKeyForInterval = rbuilder_get_random_int(100000000000,999999999999);

		// Set the input "name" values.
		var $_this = jQuery(this);

		if ($_this.find("[data-experience-part='date-range']").attr('name') == ''){
			$_this.find("[data-experience-part='date-range']").attr('name','_resume_settings[experience]['+randomKeyForInterval+'][date_range]');
		}

		if ($_this.find("[data-experience-part='short-description']").attr('name') == ''){
			$_this.find("[data-experience-part='short-description']").attr('name','_resume_settings[experience]['+randomKeyForInterval+'][short_description]');
		}

		if ($_this.find("[data-experience-part='title']").attr('name') == ''){
			$_this.find("[data-experience-part='title']").attr('name','_resume_settings[experience]['+randomKeyForInterval+'][title]');
		}

		if ($_this.find("[data-experience-part='long-description']").attr('name') == ''){
			var thisID = $_this.find("[data-experience-part='long-description']").attr('id');
			$_this.find("[data-experience-part='long-description']").attr('id', thisID + '_' + randomKeyForInterval );
			$_this.find("[data-experience-part='long-description']").attr('name','_resume_settings[experience]['+randomKeyForInterval+'][long_description]')
			try { tinyMCE.execCommand( 'mceAddEditor', false, thisID + '_' + randomKeyForInterval ); } catch(e){}
		}

		if ($_this.find("[data-experience-part='section_heading_name']").attr('name') == ''){
			$_this.find("[data-experience-part='section_heading_name']").attr('name','_resume_settings[experience]['+randomKeyForInterval+'][section_heading_name]');
		}

		if ($_this.find("[data-experience-part='section-text-content']").attr('name') == ''){
			var thisID = $_this.find("[data-experience-part='section-text-content']").attr('id');
			$_this.find("[data-experience-part='section-text-content']").attr('id', thisID + '_' + randomKeyForInterval );
			$_this.find("[data-experience-part='section-text-content']").attr('name','_resume_settings[experience]['+randomKeyForInterval+'][section_text_content]')
			try { tinyMCE.execCommand( 'mceAddEditor', false, thisID + '_' + randomKeyForInterval ); } catch(e){}
		}

	});

}

// Reset Skills Builder
function rbuilder_reset_skills_builder(){

	var skillsBlocks = jQuery('.rbuilder-skills-block');
    skillsBlocks.each(function(){

        var randomKeyForInterval = rbuilder_get_random_int(100000000000,999999999999);

        // Set the input "name" values.
        var $_this = jQuery(this);

        if ($_this.find("[data-skills-part='section_heading_name']").attr('name') == ''){
            $_this.find("[data-skills-part='section_heading_name']").attr('name','_resume_settings[skills]['+randomKeyForInterval+'][section_heading_name]');
        }

        if ($_this.find("[data-skills-part='title']").attr('name') == ''){
            $_this.find("[data-skills-part='title']").attr('name','_resume_settings[skills]['+randomKeyForInterval+'][title]');
        }

        if ($_this.find("[data-skills-part='rating']").attr('name') == ''){
            $_this.find("[data-skills-part='rating']").attr('name','_resume_settings[skills]['+randomKeyForInterval+'][rating]');
        }

        if ($_this.find("[data-skills-part='description']").attr('name') == ''){
            $_this.find("[data-skills-part='description']").attr('name','_resume_settings[skills]['+randomKeyForInterval+'][description]');
        }

    });

}

// Get random integer for sortable lists (experience and directions)
function rbuilder_get_random_int(min, max) {
	return Math.floor(Math.random() * (max - min)) + min;
}

// Check if value is an integer (for amount field in Experiences Builder)
function rbuilder_is_int(val){
	if(Math.floor(val) == val && $.isNumeric(val)){
		return true;
	} else {
		return false;
	}
}
