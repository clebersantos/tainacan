<!DOCTYPE html>

<!--[if IEMobile 7 ]> <html <?php language_attributes(); ?>class="no-js iem7"> <![endif]-->
<!--[if lt IE 7 ]> <html <?php language_attributes(); ?> class="no-js ie6"> <![endif]-->
<!--[if IE 7 ]>    <html <?php language_attributes(); ?> class="no-js ie7"> <![endif]-->
<!--[if IE 8 ]>    <html <?php language_attributes(); ?> class="no-js ie8"> <![endif]-->
<!--[if (gte IE 9)|(gt IEMobile 7)|!(IEMobile)|!(IE)]><!-->
<?php
global $current_user;
get_currentuserinfo();
$socialdb_logo = get_option('socialdb_logo');
$socialdb_title = get_option('blogname');

?>
<html <?php language_attributes(); ?> xmlns:fb="http://www.facebook.com/2008/fbml" class="no-js"><!--<![endif]-->
    <head>
        <meta charset="<?php bloginfo('charset'); ?>"><meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <meta name="google-site-verification" content="29Uww0bx9McdeJom1CDiXyGUZwK5mtoSuF5tA_i59F4" />         
        <link rel="icon" type="image/png" href="<?php echo get_template_directory_uri().'/libraries/images/icone.png' ?>">
        <title> <?php echo repository_page_title() ?> </title>
        <?php if(is_front_page()) { ?>
            <link rel="alternate" type="application/rdf+xml" href="<?php echo site_url(); ?>/?.rdf">
            <?php if(is_restful_active()){ ?>
                <link rel="alternate" type="application/json" href="<?php echo site_url(); ?>/wp-json/">
            <?php } ?>
        <?php }else if(is_page_tainacan()){ ?>
            <link rel="alternate" type="application/rdf+xml" href="<?php echo get_the_permalink(); ?>?<?php echo get_page_tainacan() ?>=<?php echo trim($_GET[get_page_tainacan()]) ?>.rdf">
            <?php if(is_restful_active()){ ?>
            <link rel="alternate" type="application/json" href="<?php echo site_url() . '/wp-json/posts/' . get_post_by_name($_GET[get_page_tainacan()],OBJECT,'socialdb_object')->ID.'/?type=socialdb_object' ?>">
            <?php } ?>    
        <?php }else if(is_single()){ ?>
            <link rel="alternate" type="application/rdf+xml" href="<?php echo get_the_permalink(); ?>?.rdf">
            <?php if(is_restful_active()){ ?>
                 <link rel="alternate" type="application/json" href="<?php echo site_url() . '/wp-json/posts/' . get_the_ID().'/?type=socialdb_collection' ?>">
            <?php } ?>
        <?php }?>    
            
            
        <?php echo set_config_return_button(is_front_page()); ?>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <?php wp_head(); ?>
    </head>
    <!-- TAINACAN: tag body adaptado para o gplus -->
    <body <?php body_class(); ?> itemscope>
    <?php
    if(is_front_page()) {
        echo home_header_bg($socialdb_logo);
    }

    // require (dirname(__FILE__) . "/models/user/facebook.php");
    global $wp_query;
    $collection_id = $wp_query->post->ID;
    $collection_owner = $wp_query->post->post_author;
    $user_owner = get_user_by('id', $collection_owner)->display_name;

//        $facebook = new Facebook(array(
//            'appId' => "1003980369621510",
//            'secret' => "3c89421b29a2862d3ea8089e84d64147",
//            'cookie' => true,
//        ));
    ?>
         <!-- TAINACAN: tag nav, utilizando classes do bootstrap nao modificadas, onde estao localizados os links que chamam paginas da administracao do repositorio -->
         <nav <?php echo set_navbar_bg_color('black'); ?> class="navbar navbar-default header-navbar">
             <!--?php wp_nav_menu( array( 'theme_location' => 'header-menu' ) ); ?-->
            <div class="container-fluid">

                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                    <span class="icon-bar"></span><span class="icon-bar"></span><span class="icon-bar"></span>
                </button>

                <div class="navbar-header logo-container">
                    <!-- TAINACAN: neste local eh mostrado a logo juntamente com o titulo do repositorio  -->
                    <?php if( $socialdb_logo != '' && get_the_post_thumbnail($socialdb_logo, 'thumbnail') ): ?>
                        <a class="navbar-brand" href="<?php echo site_url(); ?>">
                            <?php if(get_the_post_thumbnail($socialdb_logo, 'thumbnail')){ ?>
                                <img src="<?php echo wp_get_attachment_url(get_post_thumbnail_id($socialdb_logo)); ?>" style="max-width: 150px; max-height: 30px;" />
                            <?php } elseif($socialdb_title!='') {
                                echo $socialdb_title;
                            } else { _e('Tainacan','tainacan'); } ?>
                        </a>
                    <?php else: ?>
                    <a class="navbar-brand logo-tainacan" href="<?php echo site_url(); ?>">
                        <img src="<?php echo get_template_directory_uri().'/libraries/images/Tainacan_pb.svg' ?>" width="150px"/>
                    </a>
                    <?php endif;

                     if(!is_front_page()): ?>
                        <form id="formSearchCollections" class="navbar-form navbar-left search-tainacan-collection" role="search">
                            <div class="input-group search-collection search-home">
                                <input type="text" class="form-control" name="search_collections" id="search_collections" placeholder="<?php _e('Search Collection', 'tainacan') ?>"/>
                                <span class="input-group-btn">
                               <button class="btn btn-default" type="button"><span class="glyphicon glyphicon-search"></span></button>
                            </span>
                            </div>
                        </form>
                    <?php endif; ?>
                </div>
                <!-- TAINACAN: container responsavel em listar os links para as acoes no repositorio -->
                <div class="user-actions collapse navbar-collapse" id="bs-example-navbar-collapse-1">

                     <!-- TAINACAN: mostra acoes do usuario, cadastro, login, edital perfil suas colecoes -->
                    <ul class="nav navbar-nav navbar-right">
                        <?php if (is_user_logged_in()): ?>
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                    <span class="glyphicon glyphicon-user"></span>&nbsp;<?php echo $current_user->display_name; ?><span class="caret"></span>
                                </a>
                                <ul class="dropdown-menu" role="menu">
                                    <li>
                                        <a href="#" onclick="showProfileScreen('<?php echo get_template_directory_uri() ?>');"> <?php _e('Profile','tainacan'); ?></a>
                                    </li>
                                    <li>
                                        <a href="<?= get_the_permalink(get_option('collection_root_id')) . '?mycollections=true' ?>"><?php _e('My collections','tainacan'); ?></a>
                                    </li>
                                    <?php if ( current_user_can( 'manage_options' ) ): ?>
                                        <li class="divider"></li>
                                        <!-- TAINACAN: mostra acoes do repositorio dentro da tag <div id="configuration"> localizado no arquivo single.php -->
                                        <li class="admin-config-menu">
                                            <a class="config" href="javascript:void(0)"> <?php _e('Repository Configurations','tainacan'); ?> <span class="caret"></span> </a>
                                            <ul class="admin-config-submenu" aria-expanded="false">
                                                <li><a onclick="showRepositoryConfiguration('<?php echo get_template_directory_uri() ?>');" href="#"><span class="glyphicon glyphicon-wrench"></span> <?php _e('Configuration','tainacan'); ?></a></li>
                                                <li><a onclick="showPropertiesRepository('<?php echo get_template_directory_uri() ?>');" href="#"><span class="glyphicon glyphicon-list-alt"></span> <?php _e('Metadata','tainacan'); ?></a></li>
                                                <li <?php do_action('menu_repository_social_api') ?>><a onclick="showAPIConfiguration('<?php echo get_template_directory_uri() ?>');" href="#"><span class="glyphicon glyphicon-picture"></span>  <?php _e('Social / API Keys','tainacan'); ?></a></li>
                                                <li <?php do_action('menu_repository_license') ?>><a onclick="showLicensesRepository('<?php echo get_template_directory_uri() ?>');" href="#"><span class="glyphicon glyphicon-picture"></span> <?php _e('Licenses','tainacan'); ?></a></li>
                                                <li><a onclick="showEventsRepository('<?php echo get_template_directory_uri() ?>','<?php echo get_option('collection_root_id') ?>');"  href="#"> <span class="glyphicon glyphicon-flash"></span>&nbsp;<?php _e('Events','tainacan'); ?>&nbsp;&nbsp;<span id="notification_events_repository" style="background-color:red;color:white;font-size:13px;"></span></a></li>
                                                <li><a onclick="showWelcomeEmail('<?php echo get_template_directory_uri() ?>');"  href="#"><span  class="glyphicon glyphicon-envelope"></span> <?php _e('Welcome Email','tainacan'); ?></a></li>
                                                <li><a onclick="showTools('<?php echo get_template_directory_uri() ?>');"  href="#"><span  class="glyphicon glyphicon-asterisk"></span> <?php _e('Tools','tainacan'); ?></a></li>
                                            </ul>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                            </li>
                            <li><a href="<?php echo wp_logout_url(get_permalink()); ?>"><?php _e('Logout','tainacan'); ?></a></li>
                        <?php else: ?>
                                <li>
                                    <button class="btn btn-default pull-right" onclick="showLoginScreen('<?php echo get_template_directory_uri() ?>');" href="#">
                                       &nbsp;<?php _e('Login','tainacan') ?>
                                    </button>
                                </li>
                                <li>
                                    <button class="btn btn-default pull-right" id="openmyModalRegister" >
                                        &nbsp;<?php _e('Register','tainacan') ?>
                                    </button>
                                </li>
                        <?php endif; ?>
                    </ul>
                     <ul class="nav navbar-nav navbar-right repository-settings">
                        <!-- TAINACAN: mostra a busca avancada dentro da tag <div id="configuration"> localizado no arquivo single.php -->
                        <!--li><a onclick="showAdvancedSearch('<?php echo get_template_directory_uri() ?>');" href="#"><span class="glyphicon glyphicon-search"></span>&nbsp;<?php _e('Advanced Search','tainacan'); ?></a></li -->
                            <!--button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"><span class="glyphicon glyphicon-chevron-down"></span></button>
                            <!--a class="dropdown-toggle" href="#" data-toggle="dropdown">Sign In <strong class="caret"></strong></a-->
                        <!-- TAINACAN: abre o modal id="myModal" localizado neste arquivo -->   
                        <li class="click_new_collection"><a href="#" id="click_new_collection"><span class="glyphicon glyphicon-plus"></span> <?php _e('Create Collection','tainacan'); ?></a></li>
                         <!-- TAINACAN: sai da pagina e vai para a colecao raiz -->   
                        <li><a href="<?php echo get_permalink(get_option('collection_root_id')); ?>"><span class="glyphicon glyphicon-book"></span>&nbsp;<?php _e('Collections','tainacan'); ?></a></li>
                    </ul>
                </div><!-- /.navbar-collapse -->
            </div><!-- /.container-fluid -->
        </nav> 

         <!-- TAINACAN: modal padrao bootstrap aberto via javascript pelo seu id, formulario inicial para criacao de colecao -->
        <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form onsubmit="$('#myModal').modal('hide');show_modal_main();" action="<?php echo get_template_directory_uri() ?>/controllers/collection/collection_controller.php" method="POST">  
                        <input type="hidden" name="operation" value="simple_add">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="createCollectionTitleDefault"><?php _e('Select a template','tainacan'); ?>
                                <a style="margin-left: 20%;" onclick="showModalImportCollection();" href="#"><?php _e(' Or import a collection','tainacan') ?></a>
                            </h4>
                            <h4 id="createCollectionTitle" style="display: none;">
                                <?php _e('Create Collection','tainacan'); ?>
                            </h4>
                        </div>
                        <div id="form_new_collection" style="display: none;"> 
                            <div class="modal-body">

                                <div class="form-group">
                                    <label for="collection_name"><?php _e('Collection name','tainacan'); ?></label>
                                    <input type="text" required="required" class="form-control" name="collection_name" id="collection_name" placeholder="<?php _e('Type the name of your collection','tainacan'); ?>">
                                </div>
                                <div class="form-group" <?php do_action('collection_create_name_object')  ?> >
                                    <label for="collection_object"><?php _e('Collection object','tainacan'); ?></label>
                                    <input type="text" required="required" class="form-control" name="collection_object" id="collection_object"  value="<?php _e('Item'); ?>">
                                </div>
                                <input type="hidden" name="template" id='template_collection' value="">
                                <br>

                            </div>
                            <div class="modal-footer">
                                <!--button onclick="listTemplates()" type="button" class="btn btn-default" data-dismiss="modal"><?php _e('Close','tainacan'); ?></button-->
                                <button onclick="backTemplates()" type="button" class="btn btn-default"><?php _e('Back','tainacan'); ?></button>
                                <button  type="submit" class="btn btn-primary"><?php _e('Save'); ?></button>
                            </div>
                        </div> 
                        <div id="list_templates" style="margin-left: 25px;margin-right: 10px;margin-bottom: 20px;height: 50%;overflow-y: scroll;">
                            <div onclick="onClickDefaultTemplate()" class="row templates-collections" id="default_collection">
                                <div class="col-sm-3" >
                                    <img class="img-responsive" src="<?php echo get_template_directory_uri() ?>/libraries/images/default_thumbnail.png">
                                </div>   
                                 <div class="col-sm-9">
                                     <h2><?php _e('Empty Collection', 'tainacan') ?></h2>
                                     <p><?php _e('Create a default collection, extend default metadata from repository and no items inserted','tainacan') ?></p>
                                </div>   
                            </div>
                        </div>
                    </form>    
                </div>
            </div>
        </div>     
         <!-- TAINACAN: modal padrao bootstrap, aberto pelo id, responsavel pelo o cadastro de usuario -->
        <div class="modal fade" id="myModalRegister" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form  id="formUserRegister" name="formUserRegister" >  
                        <input type="hidden" name="operation" value="add">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="myModalLabel"><?php _e('Register','tainacan'); ?></h4>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="first_name"><?php _e('First Name','tainacan'); ?><span style="color: #EE0000;"> *</span></label>
                                <input type="text" required="required" class="form-control" name="first_name" id="first_name" placeholder="<?php _e('Type here your first name','tainacan'); ?>">
                            </div>
                            <div class="form-group">
                                <label for="last_name"><?php _e('Last Name','tainacan'); ?><!--span style="color: #EE0000;"> *</span--></label>
                                <input type="text" class="form-control" name="last_name" id="last_name" placeholder="<?php _e('Type here your last name','tainacan'); ?>">
                            </div>
                            <div class="form-group">
                                <label for="user_email"><?php _e('Email','tainacan'); ?><span style="color: #EE0000;"> *</span></label>
                                <input type="email" required="required" class="form-control" name="user_email" id="user_email" placeholder="<?php _e('Type here your e-mail','tainacan'); ?>">
                            </div>
                            <div class="form-group">
                                <label for="user_login"><?php _e('Username','tainacan'); ?><span style="color: #EE0000;"> *</span></label>
                                <p class="help-block"><?php _e('Help: Limit of 25 characters','tainacan'); ?></p>
                                <input onkeyup="showUserName(this)" maxlength="25" type="text" required="required" class="form-control" name="user_login" id="user_login" placeholder="<?php _e('Type here the username that you will use for login','tainacan'); ?>">
                                <span id="result_username"></span>
                            </div>
                            <div class="form-group">
                                <label for="user_pass"><?php _e('Password','tainacan'); ?><span style="color: #EE0000;"> *</span></label>
                                <input  type="password" required="required" class="form-control" name="user_pass" id="user_pass" placeholder="<?php _e('Type here your password','tainacan'); ?>">
                            </div>
                            <div class="form-group">
                                <label for="user_pass"><?php _e('Confirm Password','tainacan'); ?><span style="color: #EE0000;"> *</span></label>
                                <input type="password" required="required" class="form-control" name="user_conf_pass" id="user_conf_pass" placeholder="<?php _e('Confirm your password','tainacan'); ?>">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal"><?php _e('Close','tainacan'); ?></button>
                            <button type="submit" class="btn btn-primary" onclick="check_register_fields(); return false;"><?php _e('Register','tainacan'); ?></button>
                        </div>
                    </form>    
                </div>
            </div>
        </div>
          <!-- TAINACAN: modal padrao bootstrap aberto via javascript pelo seu id, formulario inicial para criacao de colecao -->
        <div class="modal fade" id="modalImportCollection" tabindex="-1" role="dialog" aria-labelledby="modalImportCollectionLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form id="importCollection">  
                        <input type="hidden" name="operation" value="importCollection">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="myModalLabel"><?php _e('Import Collection','tainacan'); ?></h4>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="collection_file"><?php _e('Select the file','tainacan'); ?></label>
                                <input type="file" required="required" class="form-control" name="collection_file" id="collection_file" >
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal"><?php _e('Close','tainacan'); ?></button>
                            <button type="submit" class="btn btn-primary"><?php _e('Import','tainacan'); ?></button>
                        </div>
                    </form>    
                </div>
            </div>
        </div>