<?php only_admin_access();
// d($params);
$is_momodule_comments = is_module('comments');

$post_params = $params;

if (isset($post_params['id'])) {
  $paging_param = 'curent_page' . crc32($post_params['id']);
  unset($post_params['id']);
} else {
  $paging_param = 'curent_page';
}

if (isset($post_params['paging_param'])) {
	$paging_param = $post_params['paging_param'];
}


if (isset($params['curent_page'])) {
	$curent_page = $params['curent_page'];
}

if (isset($post_params['data-page-number'])) {

  $post_params['curent_page'] = $post_params['data-page-number'];
  unset($post_params['data-page-number']);
}



if (isset($post_params['data-category-id'])) {

  $post_params['category'] = $post_params['data-category-id'];
  unset($post_params['data-category-id']);
}






if (isset($params['data-paging-param'])) {

  $paging_param = $params['data-paging-param'];

}





$show_fields = false;
if (isset($post_params['data-show'])) {
    //  $show_fields = explode(',', $post_params['data-show']);

  $show_fields = $post_params['data-show'];
} else {
  $show_fields = get_option('data-show', $params['id']);
}

if ($show_fields != false and is_string($show_fields)) {
  $show_fields = explode(',', $show_fields);
}





if (!isset($post_params['data-limit'])) {
  $post_params['limit'] = get_option('data-limit', $params['id']);
}
$cfg_page_id = false;
if (isset($post_params['data-page-id'])) {
 $cfg_page_id =   intval($post_params['data-page-id']);
} else {
  $cfg_page_id = get_option('data-page-id', $params['id']);

}
$posts_parent_category = false;
if(isset($post_params['category'])){
	$posts_parent_category = $post_params['category'];
}

if ($cfg_page_id != false and intval($cfg_page_id) > 0) {
 $sub_cats = array();

 if($posts_parent_category != false ){
   $page_categories = false;
   if(intval($cfg_page_id) != 0){
    $str0 = 'table=categories&limit=1000&data_type=category&what=categories&' . 'parent_id=[int]0&rel_id=' . $cfg_page_id;
    $page_categories = get($str0);
					// d($page_categories);
    if(isarr($page_categories)){
      foreach ($page_categories as $item_cat){
							//d($item_cat);
        $sub_cats[] = $item_cat['id'];
        $more =    get_category_children($item_cat['id']);
        if($more != false and isarr($more)){
         foreach ($more as $item_more_subcat){
          $sub_cats[] = $item_more_subcat;
        }
      }

    }
  }
}

if($posts_parent_category != false){
  if(isarr($page_categories)){
   $sub_cats = array();
   foreach ($page_categories as $item_cat){
    if(intval( $item_cat['id'] ) == intval($posts_parent_category) ){
     $sub_cats = array($posts_parent_category);
   }
 }
} else {
  $sub_cats = array($posts_parent_category);
}
}


if(isarr($sub_cats)){
  $post_params['category'] = $sub_cats;
}


}
$post_params['parent'] = $cfg_page_id;




}





$tn_size = array('150');

if (isset($post_params['data-thumbnail-size'])) {
  $temp = explode('x', strtolower($post_params['data-thumbnail-size']));
  if (!empty($temp)) {
    $tn_size = $temp;
  }
} else {
  $cfg_page_item = get_option('data-thumbnail-size', $params['id']);
  if ($cfg_page_item != false) {
    $temp = explode('x', strtolower($cfg_page_item));

    if (!empty($temp)) {
      $tn_size = $temp;
    }
  }
}







$post_params['is_deleted'] = 'y';

$content   =$data = get_content($post_params);
?>
<?php
$post_params_paging = $post_params;
//$post_params_paging['count'] = true;




$post_params_paging['page_count'] = true;
$pages = get_content($post_params_paging);

$paging_links = false;
$pages_count = intval($pages);
?>
<?php if (intval($pages_count) > 1): ?>
<?php $paging_links = paging_links(false, $pages_count, $paging_param, $keyword_param = 'keyword'); ?>
<?php endif; ?>
<h2 class="left" style="padding-left: 20px;width: 430px;padding-bottom:16px; "><?php _e("Deleted content"); ?></h2>







<div class="mw_clear"></div>



<div class="manage-toobar manage-toolbar-top">
  <span class="mn-tb-arr-top left"></span>
  <span class="posts-selector left">
    <span onclick="mw.check.all('#pages_delete_container')"><?php _e("Select All"); ?></span>/<span onclick="mw.check.none('#pages_delete_container')"><?php _e("Unselect All"); ?></span></span>
    <span onclick="delete_selected_posts_forever();" class="mw-ui-btn mw-ui-btn-medium mw-ui-btn-red"><?php _e("Delete forever"); ?></span>
    <span onclick="restore_selected_posts();" class="mw-ui-btn mw-ui-btn-medium mw-ui-btn-green"><?php _e("Restore selected"); ?></span>
    <div class="post-th"> <span class="manage-ico mAuthor"></span> <span class="manage-ico mComments"></span> </div>
  </div>



  <div class="manage-posts-holder" id="pages_delete_container">

    <?php if(isarr($data)): ?>
    <?php foreach ($data as $item): ?>

    <?php
    $pub_class = '';
    if(isset($item['is_active']) and $item['is_active'] == 'n'){
     $pub_class = ' content-unpublished';
   }

   ?>


   <div class="manage-post-item manage-post-item-<?php print ($item['id']) ?> <?php print $pub_class ?>">
    <div class="manage-post-itemleft">
      <label class="mw-ui-check left">
        <input name="select_delete_forever" class="select_delete_forever" type="checkbox" value="<?php print ($item['id']) ?>">
        <span></span></label>
        <span class="ico iMove mw_admin_posts_sortable_handle" onmousedown="mw.manage_content_sort()"></span>
        <?php
        $pic  = get_picture(  $item['id']);


        ?>
        <?php if($pic == true ): ?>
        <a class="manage-post-image left" style="background-image: url('<?php print thumbnail($pic, 108) ?>');"  onClick="mw.url.windowHashParam('action','editpost:<?php print ($item['id']) ?>');return false;"></a>
      <?php else : ?>
      <a class="manage-post-image manage-post-image-no-image left"  onClick="mw.url.windowHashParam('action','editpost:<?php print ($item['id']) ?>');return false;"></a>
    <?php endif; ?>

    <?php $edit_link = admin_url('view:content#action=editpost:'.$item['id']);  ?>

    <div class="manage-post-main">
      <h3 class="manage-post-item-title"><a target="_top" href="<?php print $edit_link ?>" onClick="mw.url.windowHashParam('action','editpost:<?php print ($item['id']) ?>');return false;"><?php print strip_tags($item['title']) ?></a></h3>
      <small><a  class="manage-post-item-link-small" target="_top"  href="<?php print content_link($item['id']); ?>/editmode:y"><?php print content_link($item['id']); ?></a></small>
      <div class="manage-post-item-description"> <?php print character_limiter(strip_tags($item['description']), 60);
      ?> </div>
      <div class="manage-post-item-links"> <a target="_top"  href="<?php print content_link($item['id']); ?>/editmode:y"><?php _e("Live edit"); ?></a> <a target="_top" href="<?php print $edit_link ?>" onClick="javascript:mw.url.windowHashParam('action','editpost:<?php print ($item['id']) ?>'); return false;"><?php _e("Edit"); ?></a> <a href="javascript:delete_single_post_forever('<?php print ($item['id']) ?>');"><?php _e("Delete forever"); ?></a> <a href="javascript:restore_single_post_from_deletion('<?php print ($item['id']) ?>');;"><?php _e("Restore"); ?></a></div>
    </div>
    <div class="manage-post-item-author" title="<?php print user_name($item['created_by']); ?>"><?php print user_name($item['created_by'],'username') ?></div>
  </div>

  <?php if($is_momodule_comments == true): ?>
  <?php $new = get_comments('count=1&is_moderated=n&content_id='.$item['id']); ?>
  <?php

  if($new > 0){
    $have_new = 1;
  } else {
    $have_new = 0;
    $new = get_comments('count=1&content_id='.$item['id']);
  }
  ?>




  <?php if($have_new): ?>

  <a href="<?php print admin_url('view:comments'); ?>/#content_id=<?php print $item['id'] ?>" class="comment-notification"><?php print($new); ?></a>

<?php else:  ?>

  <span class="comment-notification comment-notification-silver "><?php print($new); ?></span>
<?php endif;?>
<?php endif; ?>


</div>
<?php endforeach; ?>
</div>
<div class="manage-toobar manage-toolbar-bottom">
  <span class="mn-tb-arr-bottom"></span> <span class="posts-selector">
  <span onclick="mw.check.all('#pages_delete_container')"><?php _e("Select All"); ?></span>/<span onclick="mw.check.none('#pages_delete_container')"><?php _e("Unselect All"); ?></span>
</span>
<span onclick="delete_selected_posts_forever();" class="mw-ui-btn mw-ui-btn-medium mw-ui-btn-red"><?php _e("Delete forever"); ?></span>
<span onclick="restore_selected_posts();" class="mw-ui-btn mw-ui-btn-medium mw-ui-btn-green"><?php _e("Restore selected"); ?></span></div>








<script  type="text/javascript">
mw.require('forms.js', true);
</script>
<script  type="text/javascript">
mw.post_del_forever = function(a, callback){
  var arr = $.isArray(a) ? a : [a];
  var obj = {ids:arr, forever:true}
  $.post(mw.settings.site_url + "api/delete_content", obj, function(data){
    typeof callback === 'function' ? callback.call(data) : '';
  });
}
mw.post_undelete = function(a, callback){
  var arr = $.isArray(a) ? a : [a];
  var obj = {ids:arr, undelete:true}
  $.post(mw.settings.site_url + "api/delete_content", obj, function(data){
    typeof callback === 'function' ? callback.call(data) : '';
  });
}


</script>


<script type="text/javascript">
delete_selected_posts_forever = function(){


  mw.tools.confirm("<?php _e("Are you sure you want to delete those pages forever"); ?>?", function(){
      var master = mwd.getElementById('pages_delete_container');
      var arr = mw.check.collectChecked(master);
      arr.forever = true;
      mw.post_del_forever(arr, function(){
       mw.reload_module('#<?php print $params['id'] ?>', function(){
         toggle_cats_and_pages()
       });
     });
  });


}


delete_single_post_forever = function(id){
  mw.tools.confirm("<?php _e("Do you want to delete this content forever"); ?>?", function(){
       var arr = id;
       arr.forever = true;
       mw.post_del_forever(arr, function(){
        mw.$(".manage-post-item-"+id).fadeOut();
        mw.notification.success("<?php _e('Content is deleted!'); ?>");
      });
  });
}

restore_selected_posts = function(){

 mw.tools.confirm("<?php _e("Are you sure you want restore the selected content"); ?>?", function(){


     var master = mwd.getElementById('pages_delete_container');
  var arr = mw.check.collectChecked(master);
  arr.forever = true;
  mw.post_undelete(arr, function(){



   mw.reload_module("pages", function(){
    if(!!mw.treeRenderer){
     var isel = $('#pages_tree_toolbar');
     if(isel.length > 0){
       mw.treeRenderer.appendUI('.mw_pages_posts_tree');
       mw.tools.tree.recall(mwd.querySelector('.mw_pages_posts_tree'));
     }
     mw.reload_module('#<?php print $params['id'] ?>', function(){
      mw.notification.success("<?php _e('Content is restored!'); ?>");
    });

   }
 });




 });



 });




}

restore_single_post_from_deletion = function(id){
  var r=confirm("Restore this content?")
  if (r==true) {
   var arr = id;
   arr.forever = true;
   mw.post_undelete(arr, function(){
    mw.$(".manage-post-item-"+id).fadeOut();

    mw.reload_module("pages", function(){
     mw.$(".mw_pages_posts_tree").removeClass("activated");

     if(!!mw.treeRenderer){
      var isel = $('#pages_tree_toolbar');

      if(isel.length > 0){
       mw.treeRenderer.appendUI('.mw_pages_posts_tree');

       mw.tools.tree.recall(mwd.querySelector('.mw_pages_posts_tree'));
     }


     mw.notification.success("<?php _e('Content is restored!'); ?>");

     mw.reload_module('#<?php print $params['id'] ?>', function(){

     });


   }
 });






  });
	   //return false;
  }	else {
		//return false;
 }

}

</script>





<div class="mw-paging">
  <?php

  $numactive = 1;

  if(isset($params['data-page-number'])){
    $numactive   = intval($params['data-page-number']);
  } else if(isset($params['curent_page'])){
    $numactive   = intval($params['curent_page']);
  }



  if(isset($paging_links) and isarr($paging_links)):  ?>
  <?php $i=1; foreach ($paging_links as $item): ?>
  <a  class="page-<?php print $i; ?> <?php if($numactive == $i): ?> active <?php endif; ?>" href="#<?php print $paging_param ?>=<?php print $i ?>" onClick="mw.url.windowHashParam('<?php print $paging_param ?>','<?php print $i ?>');return false;"><?php print $i; ?></a>
  <?php $i++; endforeach; ?>
<?php endif; ?>

</div>

<?php else: ?>
<div class="mw-no-posts-foot">
  <?php if( isset($params['subtype']) and $params['subtype'] == 'product') : ?>
  <h2>No Products Here</h2>
  <!--  <a href="#?action=new:product" class="mw-ui-btn"><span class="ico iplus"></span><span class="ico iproduct"></span>Add New Product<b><?php print $cat_name ?></b></a>
-->
<?php else: ?>
<h2>No Posts Here</h2>
  <!--  <a href="#?action=new:post" class="mw-ui-btn"><span class="ico iplus"></span><span class="ico ipost"></span>Create New Post <b><?php print $cat_name ?></b></a> </div>
-->
<?php endif; ?>
</div>
<?php endif; ?>
