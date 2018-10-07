<!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js">
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php wp_title(); ?></title>
    <script src="<?php echo get_site_url()?>/wp-includes/js/jquery/jquery.js"></script>
    <link rel="stylesheet" href="<?php echo BLOKS_ROOT_URL?>assets/css/builder.css" />
</head>
<body <?php body_class('bloks__builder'); ?>>
    <?php while (have_posts()) : the_post();
    $backto = get_edit_post_link();
    if(!empty($_GET['return_url']))
        $backto = base64_decode($_GET['return_url']);
    ?>

    <div id="bloks__app"></div>

    <script type="text/x-template" id="bloks__app-template">
        <div class="bloks">
            <div class="bloks__header" @click="canvas = false">
                <div class="bloks__viewports">
                    <a href="javascript:void(0);" title="<?php _e('Desktop mode', BLOKS_TEXTDOMAIN)?>" v-bind:class="{ active: viewport === 'desktop' }" class="bloks__viewports-desktop" data-viewport="desktop">
                        <i class="ibloks-desktop"></i>
                    </a>
                    <a href="javascript:void(0);" title="<?php _e('Tablet mode', BLOKS_TEXTDOMAIN)?>" v-bind:class="{ active: viewport === 'tablet' }" class="bloks__viewports-tablet" data-viewport="tablet">
                        <i class="ibloks-tablet"></i>
                    </a>
                    <a href="javascript:void(0);" title="<?php _e('Mobile mode', BLOKS_TEXTDOMAIN)?>" v-bind:class="{ active: viewport === 'phone' }" class="bloks__viewports-phone" data-viewport="phone">
                        <i class="ibloks-mobile"></i>
                    </a>
                </div>

                <div class="bloks__settings">
                    <div class="bloks__page-title">
                        <input type="text" name="page-title" placeholder="<?php _e('Enter your Title', BLOKS_TEXTDOMAIN)?>" value="<?php the_title()?>" />
                    </div>
                    <a href="javascript:void(0)" title="<?php _e('Page Settings', BLOKS_TEXTDOMAIN)?>" class="bloks-tooltip" v-bind:class="{ active: pageSettings }" @click="pageSettings = !pageSettings">
                        <i class="ibloks-setting"></i>
                    </a>
                </div>

                <div class="bloks__actions">
                    <div class="bloks__actions__dropdown">
                        <button type="button" class="bloks__actions__dropdown-btn-save" @click="save" :disabled="!canSave">
                            <?php _e('Save',BLOKS_TEXTDOMAIN) ?>
                        </button>
                        <button class="bloks__actions__dropdown-toggle" :disabled="!canSave" type="button"></button>
                        <ul class="bloks__actions__dropdown__menu">
                            <li><a href="javascript:void(0)" @click="preview" ><?php _e('Save and Preview', BLOKS_TEXTDOMAIN) ?></a></li>
                            <li><a href="javascript:void(0)" @click="publish" ><?php _e('Save and Publish',BLOKS_TEXTDOMAIN) ?></a></li>
                        </ul>
                    </div>
                    <a title="<?php _e('Back to Admin', BLOKS_TEXTDOMAIN)?>" class="bloks__actions-close bloks-tooltip" href="<?php echo $backto?>">
                        <i class="ibloks-cancel"></i>
                    </a>
                </div>

                <div class="bloks__messages" v-if="message != ''" transition="flash">{{{message}}}</div>
            </div>
            <div class="bloks__canvas" v-bind:class="{ active: canvas }">
                <div class="bloks__canvas__nav">
                <?php $templates = Bloks()->getBuilderFactory()->getComponent()->getTemplates();?>
                    <ul class="bloks__canvas__nav-type">
                    <?php foreach ($templates as $type => $value):?>
                        <li>
                            <a href="javascript:void(0)" v-bind:class="{ active: hover == '<?php echo $type?>' }" @click="hover = '<?php echo $type?>'"><?php _e($value['title'], BLOKS_TEXTDOMAIN)?></a>
                        </li>
                    <?php endforeach?>
                    </ul>
                    <div class="bloks__canvas__nav-templates">
                        <?php foreach ($templates as $type => $value):?>
                        <?php if(isset($value['templates'])&&count($value['templates'])):?>
                        <div class="bloks__canvas__nav-template" v-show="hover === '<?php echo $type?>'">
                            <?php $number = 0?>
                                <?php foreach ($value['templates'] as $name => $instance):?>
                                    <?php if($instance->isAcceptPostType(get_post_type())):?>
                                        <?php $number++;
                                        echo ($number == 1)? '<ul>':'';  ?>
                                        <li>
                                            <a @click="appendComponent" data-tpl="<?php echo $type . '-' . $name ?>-template" href="javascript:void(0)">
                                                <div class="image"> <img src="<?php echo $instance->getScreenshot()?>" alt="<?php echo $instance->getName()?>" /></div>
                                                <div class="bloks-name">
                                                    <span class="label"><?php echo $instance->getName()?></span>
                                                    <?php if(isset($instance->is_custom) && $instance->is_custom):?>
                                                        <span class="is_custom"><?php _e('Custom Block', BLOKS_TEXTDOMAIN)?></span>
                                                    <?php endif; ?>
                                                </div>
                                            </a>
                                        </li>
                                        <?php if($number == 3){
                                            echo '</ul>';
                                           $number = 0; }
                                        ?>
                                    <?php endif?>
                                <?php endforeach?>
                            <?php echo (($number != 0)&&($number != 3))?'</ul>':'' ?>
                        </div>
                        <?php endif?>
                        <?php endforeach;?>
                    </div>
                </div>
                <a href="javascript:void(0);" class="bloks__canvas__button-toggle" @click="canvas = !canvas">
                    <span class="ibloks-plus"></span>
                    <?php _e('Add Block', BLOKS_TEXTDOMAIN)?>
                </a>
            </div>
            <?php $src = strpos(get_the_permalink(), '?') === false ? get_the_permalink() . '?iframe' : get_the_permalink() . '&iframe';?>
            <iframe class="bloks__iframe" src="<?php echo $src?>"></iframe>
            <div class="bloks__page-settings" v-show="pageSettings" transition="fade-down">
                <div class="bloks__page-settings-container">
                    <div class="bloks__page-settings-header">
                        <a href="javascript:void(0)" class="close" @click="pageSettings = !pageSettings"><span class="ibloks-cancel"></span></a>
                        <h4 class="bloks__page__settings-title"><?php _e('Page Settings', BLOKS_TEXTDOMAIN)?></h4>
                    </div>
                    <div class="bloks__page-settings-body">
                        <form id="bloks-meta-form">
                            <div class="form-group">
                                <label for="keywords"><?php _e('Permalink:', BLOKS_TEXTDOMAIN)?></label>
                                <div class="bloks-edit-permalink" v-bind:class="{ active: inEditSlug}">
                                    <div style="float: left;" class="sample-permalink">
                                        <a href="<?php echo get_site_url();?>?page_id=<?php echo get_the_ID();?>&preview=true" target="_blank"><?php echo get_site_url();?>/<span v-show="!inEditSlug" id="editable-post-name">{{slug}}/</span></a>
                                    </div>
                                    <div class="bloks-action-permalink">
                                        <input class="input-edit-permalink" v-show="inEditSlug" type="text" v-model="slug" v-on:keyup.enter="getSampleLink" />
                                        <span v-show="inEditSlug"></span>
                                        <a class="save" v-show="inEditSlug" v-on:click="getSampleLink" href="javascript:void(0)"><?php _e(' Save', BLOKS_TEXTDOMAIN)?></a>
                                        <a class="edit-permalink" v-show="!inEditSlug" href="javascript:void(0);" v-on:click="showInputEdit"><?php _e('Edit', BLOKS_TEXTDOMAIN)?></a>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="keywords"><?php _e('Meta Keywords:', BLOKS_TEXTDOMAIN)?></label>
                                <input id="keywords" name="meta[_meta_keywords]" type="text" class="form-control" value="<?php echo get_post_meta(get_the_ID(), '_meta_keywords', true)?>" />
                            </div>
                            <div class="form-group">
                                <label for="description"><?php _e('Meta Description:', BLOKS_TEXTDOMAIN)?></label>
                                <textarea id="description" name="meta[_meta_description]" class="form-control"><?php echo get_post_meta(get_the_ID(), '_meta_description', true)?></textarea>
                            </div>
                            <div class="form-group">
                                <label for="custom_css"><?php _e('Custom CSS:', BLOKS_TEXTDOMAIN)?></label>
                                <textarea id="custom_css" name="meta[_custom_css]" class="form-control"><?php echo get_post_meta(get_the_ID(), '_custom_css', true)?></textarea>
                            </div>
                        </form>
                    </div>
                    <div class="bloks__page-settings-footer">
                        <button type="button" class="bloks__button-save" @click="pageSettings = !pageSettings"><?php _e('Save', BLOKS_TEXTDOMAIN)?></button>
                    </div>
                </div>
            </div>
            <div class="bloks__overlay" v-show="pageSettings"></div>
            <div class="bloks__overlay-iframe" v-show="canvas" @click="canvas = false"></div>
        </div>
    </script>
    <?php endwhile;?>
    <script type="text/javascript" src="<?php echo BLOKS_ROOT_URL . 'assets/js/builder.js' ?>"></script>
    <script type="text/javascript">
        Bloks.Settings = {
            ajax: '<?php echo admin_url('admin-ajax.php')?>',
            page_id: <?php echo get_the_ID()?>,
            widgets: JSON.parse('<?php echo addslashes(json_encode(Bloks()->getWidget()->getTypes()))?>'),
            colorsets: JSON.parse('<?php echo json_encode(Bloks()->getBuilderFactory()->getColorSets())?>')
        };
    </script>

    <?php $templates = Bloks()->getBuilderFactory()->getComponent()->getTemplates();?>
    <?php foreach ($templates as $type => $value):?>
        <?php if(isset($value['templates'])&&count($value['templates'])):?>
            <?php foreach ($value['templates'] as $name => $instance):?>
                <script type="text/x-template" id="<?php echo $type . '-' . $name ?>-template">
                    <?php echo $instance->getParamsString()?>
                    <partial name="<?php echo $type . '-' . $name ?>-template"></partial>
                </script>
                <script type="text/javascript">
                    Vue.partial('<?php echo $type . '-' . $name ?>-template', '<?php echo addslashes($instance->getContent(true, true)) ?>');
                </script>
            <?php endforeach;?>
        <?php endif;?>
    <?php endforeach;?>
</body>
</html>
