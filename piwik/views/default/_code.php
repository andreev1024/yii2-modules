<?php if($model['status']): ?>
    <!-- Piwik -->
    <script type="text/javascript">
        var _paq = _paq || [];

        <?php if($model->prependTitle): ?>
            _paq.push(['setDocumentTitle', '{{title}}']);
        <?php endif; ?>

        <?php if($model->trackVisitorsAcrossAllSubdomains): ?>
            _paq.push(['setCookieDomain', '*.<?= $model['mainSiteDomen'] ?>']);
        <?php endif; ?>

        <?php if($model->trackVisitorsAcrossAllSubdomains || $model->notCountedAliasLink): ?>
            _paq.push(['setDomains', '*.<?= $model['mainSiteDomen'] ?>']);
        <?php endif; ?>

        <?php if($model->disableCookies): ?>
        _paq.push(['disableCookies']);
        <?php endif; ?>

        _paq.push(['trackPageView']);
        _paq.push(['enableLinkTracking']);

        (function() {
            var u="//<?= $model['trackerUrl'] ?>/";
            _paq.push(['setTrackerUrl', u+'piwik.php']);
            _paq.push(['setSiteId', <?= $model['siteId'] ?>]);
            var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0];
            g.type='text/javascript'; g.async=true; g.defer=true; g.src=u+'piwik.js'; s.parentNode.insertBefore(g,s);
        })();
    </script>

    <?php if($model->imageTracking): ?>
        <noscript>
            <p>
                <img src="//<?= $model['trackerUrl'] ?>/piwik.php?idsite=<?= $model['siteId'] ?>" style="border:0;" alt="" />
            </p>
        </noscript>
    <?php endif; ?>
    <!-- End Piwik Code -->
<?php endif; ?>