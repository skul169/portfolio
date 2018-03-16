<?php if(!empty($_POST['cp_do_uninstall']) && $_POST['cp_do_uninstall'] == 'yes') {

    // Uninstall Coming Soon...

    // Resume_Builder_Functions::uninstall();
    // wp_redirect('index.php');
    // exit;

}

?><div class="rbuilder-settings-header"><i class="fas fa-file-alt"></i>&nbsp;&nbsp;<?php echo sprintf( esc_html__('%s Settings','resume-builder'), 'Resume Builder' ); ?></div>

<div id="rbuilder-settings-panel" class="wrap"><?php

    global $_rbuilder_settings;
    $Resume_Builder_Settings = new Resume_Builder_Settings();
    $rbuilder_settings_tabs_fields = $Resume_Builder_Settings->tabs_fields();
    $parent_page_slug = ( isset($_rbuilder_settings['browse_page']) && $_rbuilder_settings['browse_page'] ? basename( get_permalink( $_rbuilder_settings['browse_page'] ) ) : false );

    $tab_counter = 1;
    echo '<ul id="rbuilder-settings-tabs" class="rbuilderClearFix">';
    foreach( $rbuilder_settings_tabs_fields as $tab_slug => $tab ):
        echo '<li id="rbuilder-settings-tab-' . $tab_slug . '"' . ( $tab_counter == 1 ? ' class="active"' : '' ) . '><a href="#' . $tab_slug . '"><i class="far fa-' . $tab['icon'] . '"></i>&nbsp;&nbsp;' . $tab['name'] . '</a></li>';
        $tab_counter++;
    endforeach;
    echo '</ul>';

    ?>

    <?php settings_errors(); ?>

    <div id="resume_builder_settings" class="rbuilder-settings-form-wrapper">

        <form action="options.php" method="post">

            <input type="hidden" name="rbuilder_settings_saved" value="true" />

            <?php settings_fields( 'rbuilder_settings_group' ); ?>

            <div class="rbuilder-settings-tab-content-wrapper">

                <?php

                global $conditions;
                $conditions = array();
                $conditional_requirements_js = array();
                $tab_counter = 1;

                foreach( $rbuilder_settings_tabs_fields as $tab_slug => $tab ):
                    echo '<div id="rbuilder-settings-tab-content-' . $tab_slug . '" class="rbuilder-settings-tab-content">';

                        // Add a hidden H2 tag for the messages, if any, to show up below it.
                        if ($tab_counter == 1): echo '<h2 style="display:none;"></h2>'; endif;
                        foreach( $tab['fields'] as $name => $field ):

                            $notitle = false;
                            $conditional_requirement = '';

                            $class = ( isset($field['class']) ? ' ' . $field['class'] : '' );
                            $current_val = ( isset($_rbuilder_settings[$name]) ? $_rbuilder_settings[$name] : null );
                            $field_type = 'field_' . esc_attr( $field['type'] );
                            $field_options = ( isset($field['options']) ? $field['options'] : false );
                            $field_options = ( !$field_options ? ( isset($field['placeholder']) ? $field['placeholder'] : $field_options ) : $field_options );
                            $color = ( isset($field['color']) && $field['color'] ? $field['color'] : false );

                            if ( method_exists ( $Resume_Builder_Settings, $field_type ) ):

                                if ( $field['type'] == 'misc_button' || $field['type'] == 'nonce' ):
                                    $notitle = true;
                                    $field_options = ( isset($field['title']) ? $field['title'] : false );
                                endif;

                                if ( isset($field['conditional_requirement']) && $field['conditional_requirement'] ):
                                    if ( is_array($field['conditional_requirement']) ):
                                        $conditional_requirement = ' v-show="' . implode( ' && ', $field['conditional_requirement'] ) . '"';
                                    else:
                                        $conditional_requirement = ' v-show="' . esc_attr($field['conditional_requirement']) . '"';
                                    endif;
                                endif;

                                echo ( $conditional_requirement ? '<transition name="fade">' : '' );
                                echo '<div' . $conditional_requirement . ' class="resume-setting-block ' . esc_attr( $field['type'] ) . ' rbuilder-bm-25' . $class . '">';
                                    echo ( !$notitle ? '<h3 class="rbuilder-settings-title">' . $field['title'] . '</h3>' : '' );
                                    echo ( isset($field['desc']) && $field['desc'] ? '<p>' . $field['desc']. '</p>' : '' );
                                    $Resume_Builder_Settings->$field_type( $name, $field_options, $color );
                                echo '</div>';
                                echo ( $conditional_requirement ? '</transition>' : '' );

                            endif;

                        endforeach;
                        $tab_counter++;

                    echo '</div>';
                endforeach; ?>

                <div class="rbuilder-settings-submit-wrap">
                    <?php submit_button( esc_html__( 'Update Settings','resume-builder' ), 'primary', 'submit', false ); ?>
                </div>

            </div>
        </form>
    </div>

</div>

<?php if ( !empty($conditions) ):

    foreach( $conditions as $setting_name => $array ):
        foreach( $array as $name => $true_val ):
            $val = ( isset( $_rbuilder_settings[$setting_name] ) && is_array( $_rbuilder_settings[$setting_name] ) && in_array( $true_val, $_rbuilder_settings[$setting_name] ) ? 'true' : 'false' );
            $conditional_requirements_js[] = "'$name':$val";
        endforeach;
    endforeach; ?>

    <script type="text/javascript">

        var vm = new Vue({
            el: '#rbuilder-settings-panel',
            data: {
                <?php echo implode( ',', $conditional_requirements_js ); ?>
            }
        });

    </script>

    <style type="text/css">
        .fade-enter-active { transition: opacity .5s }
        .fade-enter, .fade-leave-to { opacity: 0 }
    </style>

<?php endif;
