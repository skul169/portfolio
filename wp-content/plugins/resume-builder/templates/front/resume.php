<?php

global $resume, $resume_section, $resume_style;

if ( is_singular() && !is_admin() ):

    // Contact Info
    ob_start();

    do_action( 'rb_resume_before_contact_info' );

    $contact_info_html_left = '';
    $contact_info_html_right = '';

    if ( $resume['contact']['email'] || $resume['contact']['phone'] || $resume['contact']['website'] ):

        $contact_info = array();
        if ( isset( $resume['contact']['email'] ) && $resume['contact']['email'] ):
            $contact_info[] = '<div class="rb-resume-contact-content"><span class="rb-resume-contact-left"><i class="far fa-envelope fa-fw"></i></span><a href="mailto:' . antispambot( esc_html( $resume['contact']['email'] ) ) . '">' . antispambot( esc_html( $resume['contact']['email'] ) ) . '</a></div>';
        endif;
        if ( isset( $resume['contact']['phone'] ) && $resume['contact']['phone'] ):
            $contact_info[] = '<div class="rb-resume-contact-content"><span class="rb-resume-contact-left"><i class="far fa-phone fa-fw"></i></span>' . esc_html( $resume['contact']['phone'] ) . '</div>';
        endif;
        if ( isset( $resume['contact']['website'] ) && $resume['contact']['website'] ):
            $contact_info[] = '<div class="rb-resume-contact-content"><span class="rb-resume-contact-left"><i class="far fa-link fa-fw"></i></span><a href="' . esc_attr( $resume['contact']['website'] ) . '">' . esc_html( $resume['contact']['website'] ) . '</a></div>';
        endif;

        $contact_info_html_left = implode( '', $contact_info );

    endif;

    if ( $resume['contact']['address'] ):

        $contact_info = array();
        if ( isset( $resume['contact']['address'] ) && $resume['contact']['address'] ):
            $contact_info[] = '<div class="rb-resume-contact-content"><span class="rb-resume-contact-left"><i class="far fa-map-marker fa-fw"></i></span>' . nl2br( esc_html( $resume['contact']['address'] ) ) . '</div>';
        endif;
        $contact_info_html_right = implode( '', $contact_info );

    endif;

    if ( $contact_info_html_left && $contact_info_html_right ):
        echo '<div class="rb-resume-contact-wrapper rb-resume-clearFix">';
            echo '<span class="rb-resume-contact rb-resume-contact-section">' . $contact_info_html_left . '</span>';
            echo '<span class="rb-resume-contact rb-resume-contact-section">' . $contact_info_html_right . '</span>';
        echo '</div>';
    else:
        echo '<span class="rb-resume-contact">' . ( $contact_info_html_left ? $contact_info_html_left : $contact_info_html_right ) . '</span>';
    endif;

    do_action( 'rb_resume_after_contact_info' );

    $_HTML_contact_info = ob_get_clean();

    // Introduction
    ob_start();

    do_action( 'rb_resume_before_resume_header' );

    ?><section class="rb-resume-header<?php if ( !$resume_section && has_post_thumbnail( $resume['id'] ) || $resume_section == 'full' && has_post_thumbnail( $resume['id'] ) || $resume_section == 'intro' && has_post_thumbnail( $resume['id'] ) ): ?> rb-resume-has-photo rb-resume-clearFix<?php endif; ?>">

        <?php if ( !$resume_section && has_post_thumbnail( $resume['id'] ) || $resume_section == 'full' && has_post_thumbnail( $resume['id'] ) || $resume_section == 'intro' && has_post_thumbnail( $resume['id'] ) ): ?>
            <div class="rb-resume-photo">
                <?php echo get_the_post_thumbnail( $resume['id'], 'rb-resume-thumbnail' ); ?>
            </div>
        <?php endif; ?>

        <div class="rb-resume-intro-content">

            <?php do_action( 'rb_resume_before_title' ); ?>

            <span class="rb-resume-title"><?php echo ( isset( $resume['introduction']['title'] ) ? esc_html( $resume['introduction']['title'] ) : esc_html( $resume['title'] ) ); ?></span>

            <?php do_action( 'rb_resume_after_title' ); ?>

            <?php echo ( isset( $resume['introduction']['subtitle'] ) ? '<span class="rb-resume-subtitle">' . ( esc_html( $resume['introduction']['subtitle'] ) ) . '</span>' : '' ); ?>

            <?php echo $_HTML_contact_info; ?>

            <?php echo ( isset( $resume['introduction']['content'] ) ? '<span class="rb-resume-introduction">' . $resume['introduction']['content'] . '</span>' : '' ); ?>

            <?php do_action( 'rb_resume_after_introduction' ); ?>

        </div>

    </section><?php

    $_HTML_introduction = ob_get_clean();

    // Experience & Education
    ob_start();

    do_action( 'rb_resume_before_resume_body' );

    ?><section class="rb-resume-body"><?php

        $section_started = false;

        do_action( 'rb_resume_experience_start' );

        foreach( $resume['experience'] as $exp ):
            if ( isset( $exp['section_heading_name'] ) && $exp['section_heading_name'] ):

                if ( $section_started ): echo '</div>'; endif;
                echo '<div class="rb-resume-exp-block-wrapper">';
                    echo '<span class="rb-resume-section-heading">' . esc_html( $exp['section_heading_name'] ) . '</span>';
                    $section_started = true;

            elseif ( isset( $exp['section_text_content'] ) && $exp['section_text_content'] ):
                echo '<div class="rb-resume-exp-text-content">' . wpautop( $exp['section_text_content'] ) . '</div>';
            else:
                if ( isset( $exp['date_range'] ) && $exp['date_range'] || isset( $exp['title'] ) && $exp['title'] || isset( $exp['short_description'] ) && $exp['short_description'] || isset( $exp['long_description'] ) && $exp['long_description'] ):
                    echo '<div class="rb-resume-exp-block">';
                        echo ( isset( $exp['title'] ) && $exp['title'] ? '<span class="rb-resume-exp-name">' . esc_html( $exp['title'] ) . '</span>' : '' );
                        echo ( isset( $exp['date_range'] ) && $exp['date_range'] ? '<span class="rb-resume-exp-date-range">' . esc_html( $exp['date_range'] ) . '</span>' : '' );
                        echo ( isset( $exp['short_description'] ) && $exp['short_description'] ? '<span class="rb-resume-exp-job-degree">' . esc_html( $exp['short_description'] ) . '</span>' : '' );
                        echo ( isset( $exp['long_description'] ) && $exp['long_description'] ? '<span class="rb-resume-exp-description">' . $exp['long_description'] . '</span>' : '' );
                    echo '</div>';
                endif;
            endif;
        endforeach;

        if ( $section_started ): echo '</div>'; endif;

        do_action( 'rb_resume_experience_end' );

    ?></section><?php

    $_HTML_history = ob_get_clean();

    if ( !empty($resume['skills']) ):

        // Skills
        ob_start();

        do_action( 'rb_resume_before_resume_skills' );

        ?><section class="rb-resume-skills"><?php

            do_action( 'rb_resume_skills_start' );

            $section_started = false;
            $skill_counter = 0;

            foreach( $resume['skills'] as $skill ):

                if ( isset( $skill['section_heading_name'] ) && $skill['section_heading_name'] ):

                    if ( $skill_counter > 0 ): echo '</div>'; $skill_counter = 0; endif;
                    if ( $section_started ): echo '</div>'; $skill_counter = 0; endif;

                    echo '<div class="rb-resume-skills-block-wrapper">';
                        echo '<span class="rb-resume-section-heading">' . esc_html( $skill['section_heading_name'] ) . '</span>';
                        $section_started = true;

                else:
                    if ( isset( $skill['title'] ) && $skill['title'] || isset( $skill['description'] ) && $skill['description'] ):
                        if ( $skill_counter > 1 ): echo '</div><div class="rb-resume-clearFix">'; $skill_counter = 0; elseif ( $skill_counter == 0 ): echo '<div class="rb-resume-clearFix">'; endif;
                        echo '<div class="rb-resume-skills-block">';
                            echo ( isset( $skill['title'] ) && $skill['title'] ? '<span class="rb-resume-skill-title">' . esc_html( $skill['title'] ) . '</span>' : '' );
                            echo ( isset( $skill['rating'] ) && $skill['rating'] ? '<span class="rb-resume-skill-rating">' . Resume_Builder_Resumes::rating( $skill['rating'] ) . '</span>' : '' );
                            echo ( isset( $skill['description'] ) && $skill['description'] ? '<span class="rb-resume-skill-description">' . $skill['description'] . '</span>' : '' );
                        echo '</div>';
                        $skill_counter++;
                    endif;
                endif;

            endforeach;

            if ( $section_started ): echo '</div>'; endif;
            if ( $skill_counter > 0 ): echo '</div>'; endif;

            do_action( 'rb_resume_skills_end' );

        ?></section><?php

        do_action( 'rb_resume_after_resume_skills' );

        $_HTML_skills = ob_get_clean();

    else:

        $_HTML_skills = '';

    endif;

    // Main Template
    ?><div class="rb-resume-template-wrapper<?php echo ( $resume_style ? ' rb-resume-' . esc_attr( $resume_style ) : '' ); ?>">

        <?php do_action( 'rb_resume_before_resume' ); ?>

        <article class="rb-resume-template"><?php

            if ( !$resume_section || $resume_section == 'full' ):
                echo $_HTML_introduction . $_HTML_history . $_HTML_skills;
            elseif ( $resume_section == 'intro' ):
                echo $_HTML_introduction;
            elseif ( $resume_section == 'contact' ):
                echo '<section class="rb-resume-header"><div class="rb-resume-intro-content">';
                echo $_HTML_contact_info;
                echo '</div></section>';
            elseif ( $resume_section == 'history' ):
                echo $_HTML_history;
            elseif ( $resume_section == 'skills' ):
                echo $_HTML_skills;
            endif;

        ?></article>

        <?php do_action( 'rb_resume_after_resume' ); ?>

    </div><?php

endif;
