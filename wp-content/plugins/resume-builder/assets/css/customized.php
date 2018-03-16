<?php

$_rbuilder_settings = Resume_Builder_Settings::get();

if ( isset($_rbuilder_settings['fix_tinyfonts']) && is_array($_rbuilder_settings['fix_tinyfonts']) && in_array( 'enabled', $_rbuilder_settings['fix_tinyfonts'] ) ):

    ?>html { font-size:16px; }<?php

endif;

?>/* Heading Color */
.rb-resume-section-heading { color:<?php echo $_rbuilder_settings['headings_color']; ?>; }

/* Stars Color */
.rb-resume-skills .rb-resume-skills-block .rb-resume-skill-rating .far,
.rb-resume-skills .rb-resume-skills-block .rb-resume-skill-rating .fas,
.rb-resume-skills .rb-resume-skills-block .rb-resume-skill-rating .fal { color:<?php echo $_rbuilder_settings['stars_color']; ?>; }
