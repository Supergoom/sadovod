<!doctype html>
<html <?php language_attributes(); ?>>
<?php global $post; ?>

<head itemscope itemtype="http://schema.org/WPHeader" prefix="og: http://ogp.me/ns# fb: http://ogp.me/ns/fb# product: http://ogp.me/ns/product#">
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0">
    <meta name="yandex-verification" content="6fdabe228a5fa929" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <title itemprop="headline"><?= get_current_title() ?></title>
    <meta itemprop="description" name="description" content="<?= get_current_description() ?>">
    <meta itemprop="keywords" name="keywords" content="<?= get_current_keywords() ?>">

    <!--    <link rel="manifest" href="/manifest.json">-->
    <!--    <link rel="author" href="/humans.txt">-->

    <script src="https://cdn.tailwindcss.com"></script>

    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
    <?php wp_body_open(); ?>
    <header>
        <div class="header__top">
            <div class="container mx-auto px-4 flex justify-between h-[70px]">
                <a href="/" class="w-64 flex items-center header__logo">
                    <div>
                        <img class="lazyload" title="<?php bloginfo('name'); ?>" alt="<?php bloginfo('name'); ?>" data-src="<?php echo get_template_directory_uri(); ?>/assets/img/logo.png">
                    </div>
                    <div class="ml-2.5 text-sm/[14px] w-[175px]">
                        Союз садоводов России Город Севастополь
                    </div>
                </a>
                <div class="flex items-center header__contacts text-sm/[15px]">
                    <div class="w-[186px] flex items-center header__tel">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                            <path d="M18.3312 14.1005V16.6005C18.3322 16.8326 18.2846 17.0623 18.1917 17.275C18.0987 17.4876 17.9623 17.6785 17.7913 17.8354C17.6203 17.9923 17.4184 18.1118 17.1985 18.1861C16.9787 18.2605 16.7457 18.2881 16.5146 18.2672C13.9503 17.9886 11.4871 17.1123 9.32291 15.7089C7.30943 14.4294 5.60236 12.7223 4.32291 10.7089C2.91456 8.53487 2.03811 6.05969 1.76458 3.48386C1.74375 3.25342 1.77114 3.02116 1.84499 2.80188C1.91885 2.5826 2.03755 2.3811 2.19355 2.21021C2.34954 2.03932 2.53941 1.90279 2.75107 1.8093C2.96272 1.71581 3.19153 1.66741 3.42291 1.6672H5.92291C6.32733 1.66321 6.7194 1.80643 7.02604 2.07014C7.33269 2.33385 7.53297 2.70007 7.58958 3.10053C7.6951 3.90058 7.89078 4.68613 8.17291 5.44219C8.28503 5.74046 8.30929 6.06462 8.24283 6.37626C8.17637 6.6879 8.02196 6.97395 7.79791 7.20053L6.73958 8.25886C7.92587 10.3451 9.65329 12.0726 11.7396 13.2589L12.7979 12.2005C13.0245 11.9765 13.3105 11.8221 13.6222 11.7556C13.9338 11.6891 14.258 11.7134 14.5562 11.8255C15.3123 12.1077 16.0979 12.3033 16.8979 12.4089C17.3027 12.466 17.6724 12.6699 17.9367 12.9818C18.201 13.2937 18.3414 13.6918 18.3312 14.1005Z" stroke="#191919" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <a href="" class="ml-2.5">+7(978)720-64-25</a>
                    </div>
                    <div class="w-[186px] flex items-center header__mail">
                        <svg xmlns="http://www.w3.org/2000/svg" width="21" height="20" viewBox="0 0 21 20" fill="none">
                            <path d="M3.9987 3.33337H17.332C18.2487 3.33337 18.9987 4.08337 18.9987 5.00004V15C18.9987 15.9167 18.2487 16.6667 17.332 16.6667H3.9987C3.08203 16.6667 2.33203 15.9167 2.33203 15V5.00004C2.33203 4.08337 3.08203 3.33337 3.9987 3.33337Z" stroke="black" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M18.9987 5L10.6654 10.8333L2.33203 5" stroke="black" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <a href="" class="ml-2.5">0101@01010101.ru</a>
                    </div>
                    <div class="w-[186px] flex items-center header__working-hours">
                        <svg xmlns="http://www.w3.org/2000/svg" width="21" height="20" viewBox="0 0 21 20" fill="none">
                            <path d="M10.3294 18.3333C14.9318 18.3333 18.6628 14.6023 18.6628 9.99996C18.6628 5.39759 14.9318 1.66663 10.3294 1.66663C5.72705 1.66663 1.99609 5.39759 1.99609 9.99996C1.99609 14.6023 5.72705 18.3333 10.3294 18.3333Z" stroke="black" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M10.332 5V10L13.6654 11.6667" stroke="black" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <div class="ml-2.5">Вт и Пт: 9:00 - 14:00</div>
                    </div>
                </div>
                <div class="text-sm/[14px] flex items-center hedaer__government">
                    <img class="lazyload" data-src="<?php echo get_template_directory_uri(); ?>/assets/img/sev-logo.png" width="38px" height="40" alt="Правительство Севастополя">
                    <div class="ml-2.5">Правительство Севастополя</div>
                </div>
            </div>
        </div>
        <div class="header__nav bg-main-blue">
            <div class="container mx-auto px-4">
                <nav>
                    <ul>
                        <li>
                            <a href="/">Главная</a>
                        </li>
                        <li>
                            <a href="">Новости</a>
                        </li>
                        <li>
                            <a href="">О союзе</a>
                        </li>
                        <li>
                            <a href="">Документы</a>
                        </li>
                        <li>
                            <a href="">Партнеры</a>
                        </li>
                        <li>
                            <a href="">Список СНТ</a>
                        </li>
                        <li>
                            <a href="">Контакты</a>
                        </li>
                    </ul>
                    <div class="flex gap-[20px] items-center">
                        <?php if (!is_user_logged_in()) : ?>
                            <a href="/login" class="heacer__sing-in">Войти</a>
                            <a href="/register" class="heacer__sing-up">Регистрация</a>
                        <?php else : ?>
                            <a href="#">
                                <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 26 26" fill="none">
                                    <path d="M20.2235 8.45004C20.2235 6.49628 19.4626 4.62254 18.1082 3.24102C16.7538 1.8595 14.9168 1.08337 13.0013 1.08337C11.0858 1.08337 9.24885 1.8595 7.89442 3.24102C6.53999 4.62254 5.77908 6.49628 5.77908 8.45004C5.77908 17.0445 2.16797 19.5 2.16797 19.5H23.8346C23.8346 19.5 20.2235 17.0445 20.2235 8.45004Z" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M15.1654 23.8334C14.9452 24.1629 14.6291 24.4364 14.2489 24.6265C13.8686 24.8166 13.4375 24.9167 12.9987 24.9167C12.5599 24.9167 12.1288 24.8166 11.7485 24.6265C11.3683 24.4364 11.0522 24.1629 10.832 23.8334" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </a>
                            <a href="/account" class="">
                                <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 26 26" fill="none">
                                    <path d="M13 1C6.3724 1 1 6.3724 1 13C1 19.6276 6.3724 25 13 25C19.6276 25 25 19.6276 25 13C25 6.3724 19.6276 1 13 1Z" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M3.72266 20.615C3.72266 20.615 6.39746 17.1998 12.9975 17.1998C19.5975 17.1998 22.2735 20.615 22.2735 20.615M12.9975 12.9998C13.9522 12.9998 14.8679 12.6205 15.543 11.9454C16.2182 11.2703 16.5975 10.3546 16.5975 9.3998C16.5975 8.44503 16.2182 7.52935 15.543 6.85422C14.8679 6.17909 13.9522 5.7998 12.9975 5.7998C12.0427 5.7998 11.127 6.17909 10.4519 6.85422C9.77674 7.52935 9.39746 8.44503 9.39746 9.3998C9.39746 10.3546 9.77674 11.2703 10.4519 11.9454C11.127 12.6205 12.0427 12.9998 12.9975 12.9998Z" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </a>
                        <?php endif; ?>
                    </div>
                </nav>
            </div>
        </div>
    </header>