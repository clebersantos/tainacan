<?php
get_header();
$options = get_option('socialdb_theme_options');
?>
<!-- TAINACAN: hiddeNs responsaveis em realizar acoes do repositorio -->
<input type="hidden" id="src" name="src" value="<?php echo get_template_directory_uri() ?>">
<input type="hidden" id="repository_main_page" name="repository_main_page" value="true">
<input type="hidden" id="info_messages" name="info_messages" value="<?php
if (isset($_GET['info_messages'])) {
    echo $_GET['info_messages'];
}
?>">
 <!-- PAGINA DO ITEM -->
    <input type="hidden" id="object_page" name="object_page" value="<?php
    if (isset($_GET['item'])) {
        echo trim($_GET['item']);
    }
    ?>">
    <!-- PAGINA DA CATEGORIA -->
    <input type="hidden" id="category_page" name="category_page" value="<?php
    if (isset($_GET['category'])) {
        echo trim($_GET['category']);
    }
    ?>">
    <!-- PAGINA DA PROPRIEDADE -->
    <input type="hidden" id="property_page" name="property_page" value="<?php
    if (isset($_GET['category'])) {
        echo trim($_GET['category']);
    }
    ?>">
    <!-- PAGINA DA TAG -->
    <input type="hidden" id="tag_page" name="tag_page" value="<?php
    if (isset($_GET['tag'])) {
        echo trim($_GET['tag']);
    }
    ?>">
    <!-- PAGINA DA TAXONOMIA -->
    <input type="hidden" id="tax_page" name="object_page" value="<?php
    if (isset($_GET['tax'])) {
        echo trim($_GET['tax']);
    }
    ?>">
<input type="hidden" id="socialdb_fb_api_id" name="socialdb_fb_api_id" value="<?php echo $options['socialdb_fb_api_id']; ?>">
<input type="hidden" id="socialdb_embed_api_id" name="socialdb_embed_api_id" value="<?php echo $options['socialdb_embed_api_id']; ?>">
<input type="hidden" id="collection_id" name="collection_id" value="<?php echo get_option('collection_root_id'); ?>">
<!-- TAINACAN: classe pura jumbotron do bootstrap, so textos que foram alterados -->

<div id="main_part" class="home">
        <div class="row container-fluid">
        <div class="project-info">
        <center>
            <h1> <?php bloginfo('name') ?> </h1>
            <h3> <?php bloginfo('description') ?> </h3>
        </center>
        </div>
        <div id="searchBoxIndex" class="col-md-3 col-sm-12 center">
               <form id="formSearchCollections" role="search">
                   <div class="input-group search-collection search-home">
                       <input type="text" class="form-control" name="search_collections" id="search_collections" onfocus="changeBoxWidth(this)" placeholder="<?php _e('Find', 'tainacan') ?>"/>
                       <span class="input-group-btn">
                           <button class="btn btn-default" type="submit"><span class="glyphicon glyphicon-search"></span></button>
                        </span>
                    </div>
               </form>
               <a onclick="showAdvancedSearch('<?php echo get_template_directory_uri() ?>');" href="#" class="col-md-12 adv_search">
                   <span class="white"><?php _e('Advanced search', 'tainacan') ?></span>
               </a>
         </div>
    </div>
</div>
</header>

<!-- TAINACAN: esta div (AJAX) recebe html E esta presente tanto na index quanto no single, pois algumas views da administracao sao carregadas aqui -->
<div id="configuration"></div>
<input type="hidden" id="max_collection_showed" name="max_collection_showed" value="20">
<input type="hidden" id="total_collections" name="total_collections" value="">
<input type="hidden" id="last_index" name="last_index" value="0">

<?php if ( has_nav_menu("menu-ibram") ):
     include_once ("views/home/home_ibram.php");
    else: ?>
    <div id="display_view_main_page" class="container-fluid"></div>
<?php endif; ?>

<!-- TAINACAN: esta div possui um gif que e colocada como none quando a listagem de recents e populares  -->
<div id="loader_collections">
    <img src="<?php echo get_template_directory_uri() . '/libraries/images/new_loader.gif' ?>" width="64px" height="64px" />
    <h3> <?php _e('Loading Collections...', 'tainacan') ?> </h3>
</div>
</body>
<?php get_footer(); ?>