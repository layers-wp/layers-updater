<?php $user = wp_get_current_user(); ?>
<?php $builder_pages = hatch_get_builder_pages(); ?>
<section class="hatch-container hatch-content-large">

    <div class="hatch-row hatch-well hatch-content-large hatch-push-bottom">
        <div class="hatch-section-title">
            <h4 class="hatch-heading"><?php _e(' Hatch Updater' , HATCH_UPDATER_SLUG ); ?></h4>
            <p class="hatch-excerpt">
                <?php _e( 'This page will help you manage your Hatch API credentials allowing you to receive updates from the Obox server.' , HATCH_UPDATER_SLUG ); ?>
            </p>
        </div>
        <div class="hatch-row">
            <div class="hatch-column hatch-span-12">
                <div class="hatch-section-title hatch-tiny">
                    <p class="hatch-form-item">
                        <label class="hatch-heading"><?php _e( 'Obox Username:', HATCH_UPDATER_SLUG ); ?></label>
                        <input />
                    </p>
                    <p class="hatch-form-item">
                        <label class="hatch-heading"><?php _e( 'Obox API key:', HATCH_UPDATER_SLUG ); ?></label>
                        <input />
                    </p>
                    <p class="hatch-form-item">
                        <button class="hatch-button btn-primary btn-large"><?php _e( 'Verify' , HATCH_UPDATER_SLUG ); ?></button>
                    </p>
                    <p><em><?php _e( 'Follow this link to get your API credentials <a href="http://oboxthemes.com/api">Obox Themes API</a>', HATCH_UPDATER_SLUG ); ?></em></p>
                </div>
            </div>
        </div>
    </div>
    <footer class="hatch-row">
        <p>
            <?php _e( 'Hatch is a product of <a href="http://oboxthemes.com/">Obox Themes</a>. For questions and feedback please <a href="mailto:david@obox.co.za">email David directly</a>.', HATCH_UPDATER_SLUG ); ?>
        </p>
    </footer>

</section>

<?php $this->footer(); ?>