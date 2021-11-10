<?php
/**
 * head.php
 * 
 * <head>
 * 
 * @author      熊猫小A
 * @version     2019-01-15 0.1
 */
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
$setting = $GLOBALS['VOIDSetting']; 

if (isset($_POST['void_action'])) {
    if ($_POST['void_action'] == 'getLoginAction') {
        echo $this->options->loginAction;
        exit;
    }
}

if (!empty($setting['assetsCDN'])) {
    $assetsUrl = $setting['assetsCDN'];
}
else {
    $assetsUrl = $this->options->themeUrl.'/assets';
}
?>
<!DOCTYPE HTML>
<html>
    <head>
    <meta charset="<?php $this->options->charset(); ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="renderer" content="webkit">
    <meta name="HandheldFriendly" content="true">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta name="applicable-device" content="pc,mobile" />
    <meta http-equiv="Cache-Control" content="no-transform " />
    <meta http-equiv="Cache-Control" content="no-siteapp" />
    <?php 
    $banner = '';
    $description = '';
    if($this->is('post') || $this->is('page')){
        if($this->fields->banner != '')
            $banner=$this->fields->banner;
        if($this->fields->excerpt != '')
            $description = $this->fields->excerpt;
    }else{
        $description = Helper::options()->description;
    }
    ?>
    <title><?php Contents::title($this); ?><?php if ($this->is('index')): ?><?php if (!empty($setting['subtitle'])): ?><?php echo ' - '.$setting['subtitle']; ?><?php endif; ?><?php endif; ?></title>
    <meta name="author" content="<?php $this->author(); ?>" />
    <meta name="description" content="<?php if($description != '') echo $description; else $this->excerpt(80); ?>" />
    <?php $this->header('commentReply=&description=&wlw=&xmlrpc=&rss2=&atom=&rss1=&template=&pingback=&generator'); ?>
    <?php if($this->is('post') || $this->is('page')) {?>
    <meta property="og:title" content="<?php Contents::title($this); ?>" />
    <meta property="og:description" content="<?php if($description != '') echo $description; else $this->excerpt(80); ?>" />
    <meta property="og:site_name" content="<?php Contents::title($this); ?>" />
    <meta property="og:type" content="<?php if($this->is('post') || $this->is('page')) echo 'article'; else echo 'website'; ?>" />
    <meta property="og:url" content="<?php $this->permalink(); ?>" />
    <meta property="og:image" content="<?php echo $banner; ?>" />
    <meta property="article:published_time" content="<?php echo date('c', $this->created); ?>" />
    <meta property="article:modified_time" content="<?php echo date('c', $this->modified); ?>" />
    <meta name="twitter:title" content="<?php Contents::title($this); ?>" />
    <meta name="twitter:description" content="<?php if($description != '') echo $description; else $this->excerpt(50); ?>" />
    <meta name="twitter:card" content="summary" />
    <meta name="twitter:image" content="<?php echo $banner; ?>" /> <?php }
    else {?>
    <?php } ?>

    <!--canonical-->
    <?php if ($this->is('index')): ?><link rel="canonical" href="<?php $this->options->siteUrl(); ?>">
    <?php endif; ?>
    <?php if($this->is('post') || $this->is('page')): ?><link rel="canonical" href="<?php $this->permalink(); ?>">
    <?php endif; ?>

    <!--CSS-->
    <link rel="stylesheet" href="<?php echo $assetsUrl.'/bundle.css'; ?>">
    <link rel="stylesheet" href="<?php echo $assetsUrl.'/VOID.css'; ?>">

    <!--JS-->
    <script src="<?php echo $assetsUrl.'/bundle-header.js'; ?>"></script>
    <script>
    VOIDConfig = {
        PJAX : <?php echo $setting['pjax'] ? 'true' : 'false'; ?>,
        searchBase : "<?php Utils::index("/search/"); ?>",
        home: "<?php Utils::index("/"); ?>",
        buildTime : "<?php Utils::getBuildTime(); ?>",
        enableMath : <?php echo $setting['enableMath'] ? 'true' : 'false'; ?>,
        lazyload : <?php echo $setting['lazyload'] ? 'true' : 'false'; ?>,
        colorScheme:  <?php echo $setting['colorScheme']; ?>,
        headerMode: <?php echo $setting['headerMode']; ?>,
        followSystemColorScheme: <?php echo $setting['followSystemColorScheme'] ? 'true' : 'false'; ?>,
        VOIDPlugin: <?php echo $setting['VOIDPlugin'] ? 'true' : 'false'; ?>,
        votePath: "<?php Utils::index('/action/void?'); ?>",
        lightBg: "",
        darkBg: "",
        lineNumbers: <?php echo $setting['lineNumbers'] ? 'true' : 'false'; ?>,
        darkModeTime: {
            'start': <?php echo $setting['darkModeTime']['start']; ?>,
            'end': <?php echo $setting['darkModeTime']['end']; ?>
        },
        horizontalBg: <?php echo empty($setting['siteBg']) ? 'false' : 'true'; ?>,
        verticalBg: <?php echo empty($setting['siteBgVertical']) ? 'false' : 'true'; ?>,
        indexStyle: <?php echo $setting['indexStyle']; ?>,
        version: <?php echo $GLOBALS['VOIDVersion'] ?>,
        isDev: true
    }
    </script>
    <script src="<?php echo $assetsUrl.'/header.js'; ?>"></script>

    <?php echo $setting['head']; ?>
    <style>
        <?php if(!empty($setting['desktopBannerHeight'])): ?>
        @media screen and (min-width: 768px){
            main>.lazy-wrap{min-height: <?php echo $setting['desktopBannerHeight']; ?>vh;}
        }
        <?php endif; ?>

        <?php if(!empty($setting['mobileBannerHeight'])): ?>
        @media screen and (max-width: 768px){
            main>.lazy-wrap{min-height: <?php echo $setting['mobileBannerHeight']; ?>vh;}
        }
        <?php endif; ?>
    </style>

    <?php if (array_key_exists('src', $setting['brandFont']) && !empty($setting['brandFont']['src'])): ?>
    <style>
    @font-face {
        font-family: "BrandFont";
        src: url("<?php echo $setting['brandFont']['src']; ?>");
    }
    .brand {
        font-family: BrandFont, sans-serif;
        font-style: <?php echo $setting['brandFont']['style']; ?>!important;
        font-weight: <?php echo $setting['brandFont']['weight']; ?>!important;
    }
    </style>
    <?php endif; ?>

    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,700&display=swap" rel="stylesheet">
    <?php if(Utils::isSerif($setting)): ?>
        <link id="stylesheet_noto" href="https://fonts.googleapis.com/css?family=Noto+Serif+SC:300,400,700&display=swap&subset=chinese-simplified" rel="stylesheet">
    <?php endif; ?>

    <?php if($setting['useFiraCodeFont']): ?>
        <link href="https://fonts.googleapis.com/css?family=Fira+Code&display=swap" rel="stylesheet">
        <style>.yue code, .yue tt {font-family: "Fira Code", Menlo, Monaco, Consolas, "Courier New", monospace}</style>
    <?php endif; ?>

    </head>
