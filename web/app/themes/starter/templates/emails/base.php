<?php



if (! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

?>
<!DOCTYPE html>
<html dir="<?php echo is_rtl() ? 'rtl' : 'ltr'?>">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=<?php bloginfo('charset'); ?>" />
        <title><?php echo get_bloginfo('name', 'display'); ?></title>
    </head>
    <body <?php echo is_rtl() ? 'rightmargin' : 'leftmargin'; ?>="0" marginwidth="0" topmargin="0" marginheight="0" offset="0">
        <div id="wrapper" dir="<?php echo is_rtl() ? 'rtl' : 'ltr'?>">
            <table border="0" cellpadding="0" cellspacing="0" height="100%" width="100%">
                <tr id="header_banner">
                    <td align="center">
                        <table border="0" cellpadding="0" cellspacing="0" width="600">
                            <tr>
                                <td id="template_header_image">
                                    <div>
                                        <p style="text-align:center;"><img width="180" src="<?= app()->assets()->url('images/logo-dark-transparent.png') ?>" alt="<?= get_bloginfo('name') ?>"></p>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td align="center" valign="top">
                        <table border="0" cellpadding="0" cellspacing="0" width="600" id="template_container">
                            <tr>
                                <td align="center" valign="top">
                                    <!-- Body -->
                                    <table border="0" cellpadding="0" cellspacing="0" width="600" id="template_body">
                                        <tr>
                                            <td valign="top" id="body_content">
                                                <!-- Content -->
                                                <table border="0" cellpadding="20" cellspacing="0" width="100%">
                                                    <?php if (!empty($email->email_title)) : ?>
                                                        <tr>
                                                            <td id="header_wrapper">
                                                                <h1 style="text-align: center;"><?php echo $email->email_title; ?></h1>
                                                            </td>
                                                        </tr>
                                                    <?php endif; ?>
                                                    <tr>
                                                        <td valign="top">
                                                            <div id="body_content_inner">
                                                                <!-- Dynamic -->
                                                                <?= $email->message ?>
                                                                <!-- End Dynamic -->
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </table>
                                                <!-- End Content -->
                                            </td>
                                        </tr>
                                    </table>
                                    <!-- End Body -->
                                </td>
                            </tr>
                            <tr>
                                <td align="center" valign="top">
                                    <!-- Footer -->
                                    <table border="0" cellpadding="10" cellspacing="0" width="600" id="template_footer">
                                        <tr>
                                            <td valign="top">
                                                <table border="0" cellpadding="10" cellspacing="0" width="100%">
                                                    <tr>
                                                        <td colspan="2" valign="middle" id="credit">
                                                            <p>
                                                                <a href="<?= home_url('/') ?>" target="_blank"><?= home_url() ?></a><br/>
                                                                <small>Copyright © <?= date('Y') ?> <?= get_bloginfo('name') ?>. All Rights Reserved</small>
                                                            </p>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                    <!-- End Footer -->
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </div>
    </body>
</html>
