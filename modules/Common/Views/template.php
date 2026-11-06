<?php

use Core\Formatter;

?>
<!DOCTYPE html>
<html data-layout="<?= ($userData?->preferences["CommonBase.layout"]) ?? 'metro' ?>" lang="<?= t('Common.lang') ?>">
<head>
    <title><?= htmlspecialchars($this->getTitle()) ?></title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="/dist/main.css" rel="stylesheet" type="text/css">
    <meta name="theme-color" content="#d7ee1b">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Figtree:ital,wght@0,300..900;1,300..900&display=swap" rel="stylesheet">
    <?php foreach ($this->getHeadLinks() ?? [] as $link) {
        echo '<link ';
        foreach ($link as $key => $value) {
            echo htmlspecialchars($key).'="'.htmlspecialchars($value).'" ';
        }
        echo '>';
    } ?>

    <?= $this->headerHtml() ?>
</head>
<body>
<header>
    <h1><a href="/">Verdant Notes</a></h1>
    <?php
    if (empty($userData)) {
        ?>
        <a href="/Authorization" class="login-button">Login/Register</a>
        <?php
    } else {
        ?>
        <div class="loginInfo">
            <button class="loginInfo-btn"><?= htmlspecialchars($userData->name) ?></button>
            <div class="loginInfo-expandable">
                <span class="icon-user"><?= htmlspecialchars($userData->name) ?></span>
                <a href="/VerdantNotebook/my" class="button">Moje notesy</a>
                <a href="/User/myAccount" class="button">Moje konto</a>
                <button class="button logoutMyselfBtn" title="Wyloguj">Wyloguj</button>
            </div>
        </div>
        <?php
    }
    ?>
</header>
<main data-views="main"><?php $this->showViews('main'); ?></main>
<footer>
    <p class="footerLine">
        Verdant Notes
        <span>•</span>
        <a href="https://github.com/GreenCodeStudio/VerdantNotes">GitHub</a>
    </p>
    <p class="footerLine">
        <strong>Green Code Studio</strong>
        <span>•</span>
        <a href="https://green-code.studio/">green-code.studio</a>
        <span>•</span>
        <a href="mailto:contact@green-code.studio">contact@green-code.studio</a>
        <span>•</span>
        <a href="tel:+48783769544">+48 783 769 544</a>
        <span>•</span>
        <span>ul. Oleska 264, 42-161 Starokrzepice</span>
        <span>•</span>
        <span>NIP: 5741970332, REGON: 387817729</span>
    </p>
<!--    <div class="languageSelect">-->
<!--        <ul>-->
<!--            <li><a href="?lang=pl">PL</a></li>-->
<!--            <li><a href="?lang=en">EN</a></li>-->
<!--            <li><a href="?lang=de">DE</a></li>-->
<!--            <li><a href="?lang=fr">FR</a></li>-->
<!--            <li><a href="?lang=es">ES</a></li>-->
<!--            <li><a href="?lang=it">IT</a></li>-->
<!--            <li><a href="?lang=pt">PT</a></li>-->
<!--            <li><a href="?lang=ru">RU</a></li>-->
<!--            <li><a href="?lang=uk">UK</a></li>-->
<!--            <li><a href="?lang=zh">ZH</a></li>-->
<!--            <li><a href="?lang=ja">JA</a></li>-->
<!--            <li><a href="?lang=ko">KO</a></li>-->
<!--            <li><a href="?lang=ar">AR</a></li>-->
<!--            <li><a href="?lang=hi">HI</a></li>-->
<!--            <li><a href="?lang=cs">CS</a></li>-->
<!--            <li><a href="?lang=sk">SK</a></li>-->
<!--            <li><a href="?lang=nl">NL</a></li>-->
<!--            <li><a href="?lang=tr">TR</a></li>-->
<!--            <li><a href="?lang=sv">SV</a></li>-->
<!--            <li><a href="?lang=fi">FI</a></li>-->
<!--            <li><a href="?lang=da">DA</a></li>-->
<!--            <li><a href="?lang=no">NO</a></li>-->
<!--            <li><a href="?lang=ro">RO</a></li>-->
<!--            <li><a href="?lang=bg">BG</a></li>-->
<!--            <li><a href="?lang=el">EL</a></li>-->
<!--            <li><a href="?lang=hr">HR</a></li>-->
<!--            <li><a href="?lang=sr">SR</a></li>-->
<!--            <li><a href="?lang=sl">SL</a></li>-->
<!--            <li><a href="?lang=lt">LT</a></li>-->
<!--            <li><a href="?lang=hu">HU</a></li>-->
<!--            <li><a href="?lang=ua">UA</a></li>-->
<!--            <li><a href="?lang=ru">RU</a></li>-->
<!--            <li><a href="?lang=gr">GR</a></li>-->
<!--            <li><a href="?lang=eo">EO</a></li>-->
<!--            <li><a href="?lang=he">HE</a></li>-->
<!--            <li><a href="?lang=szl">SZL</a></li>-->
<!--        </ul>-->
<!--    </div>-->
</footer>
<script>
    //<![CDATA[
    window.controllerInitInfo = <?=json_encode($this->getInitInfo())?>;
    window.DEBUG = <?=json_encode($this->isDebug())?>;
    window.serverTimeDiff =new Date() - <?=microtime(true) * 1000?>;
    window.getServerDate = function() {
        return new Date(new Date().getTime() - window.serverTimeDiff);
    };
    //]]>
</script>
<script src="/dist/main.js" type="text/javascript"></script>
</body>
</html>
