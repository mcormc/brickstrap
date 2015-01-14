<?php defined('C5_EXECUTE') or die("Access Denied."); $fp = FilePermissions::getGlobal(); $tp = new TaskPermission(); $pageSelector = Loader::helper('form/page_selector'); ?>


<style type="text/css">
.redactor_editor{padding:20px}
.select-image{display:block;padding:15px;cursor:pointer;background:#dedede;border:1px solid #cdcdcd;text-align:center;color:#333;vertical-align:center}
.select-image img{max-width:100%}
.panel-heading{cursor:move}
.panel-body{display:none}
</style>


<?php  print Loader::helper('concrete/ui')->tabs(array(
    array('pane-items', t('Items'), true),
    array('pane-settings', t('Settings'))
));?>

<div class="ccm-tab-content" id="ccm-tab-content-pane-items">
    <div class="well bg-info"><?php  echo t('You can rearrange items if needed.'); ?></div>
    <div class="items-container"><!-- DYNAMIC ITEMS WILL GET LOADED INTO HERE --></div>
    <span class="btn btn-success btn-add-item"><?php  echo t('Add Item') ?></span>
</div>

<div class="ccm-tab-content" id="ccm-tab-content-pane-settings">
    <div class="row">
        <div class="col-xs-12">            
            <div class="form-group">
                <label for="title"><?php echo t("Carousel Title"); ?></label>
                <?php echo $form->text('title', $title); ?>

            </div>
        </div>
    </div>
</div>

<script type="text/template" id="item-template">
    <div class="item panel panel-default" data-order="<%=sort%>">
        <div class="panel-heading">
            <div class="row">
                <div class="col-xs-6">
                    <h5><i class="fa fa-arrows drag-handle"></i>
                    <?php echo t('Item'); ?> <%=parseInt(sort)+1%></h5>
                </div>
                <div class="col-xs-6 text-right">
                    <a href="javascript:editItem(<%=sort%>);" class="btn btn-edit-item btn-default"><?php echo t('Edit'); ?></a>
                    <a href="javascript:deleteItem(<%=sort%>)" class="btn btn-delete-item btn-danger"><?php echo t('Delete'); ?></a>
                </div>
            </div>
        </div>
        <div class="panel-body form-horizontal">
            <div class="form-group">
                <label class="col-xs-3 control-label"><?php echo t('Select Image'); ?></label>
                <div class="col-xs-9">
                    <a href="javascript:chooseImage(<%=sort%>);" class="select-image" id="select-image-<%=sort%>">
                        <% if (thumb.length > 0) { %>
                            <img src="<%= thumb %>" />
                        <% } else { %>
                            <i class="fa fa-picture-o"></i>
                        <% } %>
                    </a>
                    <input type="hidden" name="<?php echo $view->field('fID'); ?>[]" class="image-fID" value="<%=fID%>" />
                </div>
            </div>
            <div class="form-group">
                <label class="col-xs-3 control-label" for="headline<%=sort%>"><?php echo t('Headline'); ?></label>
                <div class="col-xs-9"><input type="text" name="headline[]" id="headline<%=sort%>" class="form-control" value="<%=headline%>"></div>
            </div>
            <div class="form-group">
                <label class="col-xs-3 control-label" for="content<%=sort%>"><?php echo t('Content'); ?></label>
                <div class="col-xs-9"><textarea name="content[]" id="content<%=sort%>" class="redactor-content"><%=content%></textarea></div>
            </div>
            <div class="form-group">
                <label class="col-xs-3 control-label"><?php echo t('Page'); ?></label>
                <div class="col-xs-9" id="select-page-<%=sort%>"><?php $this->inc('elements/page_selector.php'); ?></div>
            </div>
            <div class="form-group">
                <label class="col-xs-3 control-label" for="button<%=sort%>"><?php echo t('Button'); ?></label>
                <div class="col-xs-9"><input type="text" name="button[]" id="button<%=sort%>" class="form-control" value="<%=button%>"></div>
            </div>
            <input type="hidden" name="<?php echo $view->field('sort'); ?>[]" class="item-sort" value="<%=sort%>"/>
        </div>
    </div>
</script>

<script type="text/javascript">
    var editItem = function(i){
        $(".item[data-order='"+i+"']").find(".panel-body").toggle();
    };
    var deleteItem = function(i) {
        var confirmDelete = confirm('<?php echo t('Are you sure?'); ?>');
        if(confirmDelete == true) {
            $(".item[data-order='"+i+"']").remove();
            indexItems();
        }
    };
    var chooseImage = function(i){
        var imgShell = $('#select-image-'+i);
        ConcreteFileManager.launchDialog(function (data) {
            ConcreteFileManager.getFileDetails(data.fID, function(r) {
                jQuery.fn.dialog.hideLoader();
                var file = r.files[0];
                imgShell.html(file.resultsThumbnailImg);
                imgShell.next('.image-fID').val(file.fID)
            });
        });
    };
    function indexItems(){
        $('.items-container .item').each(function(i) {
            $(this).find('.item-sort').val(i);
            $(this).attr("data-order",i);
        });
    };
    $(function(){
        var CCM_EDITOR_SECURITY_TOKEN = "<?php echo Loader::helper('validation/token')->generate('editor'); ?>";
        var itemsContainer = $('.items-container');
        var itemTemplate = _.template($('#item-template').html());
        $(".items-container").sortable({
            handle: ".panel-heading",
            update: function(){
                indexItems();
            }
        });<?php

        if($items) { foreach ($items as $item) { ?>

        itemsContainer.append(itemTemplate({
            fID: '<?php echo $item['fID']; ?>',
            thumb: '<?php if($item['fID']) echo File::getByID($item['fID'])->getThumbnailURL('file_manager_listing'); ?>',
            cID: '<?php echo $item['cID']; ?>',
            headline: '<?php echo addslashes($item['headline']); ?>',
            content: '<?php echo str_replace(array("\t", "\r", "\n"), "", addslashes($item['content'])); ?>',
            pageName: '<?php if($item['cID']) echo Page::getByID($item['cID'])->getCollectionName(); ?>',
            button: '<?php echo addslashes($item['button']); ?>',            
            sort: '<?php echo $item['sort'] ?>'
        }));<?php

        }} ?>

        indexItems();
        $('.redactor-content').redactor({
            minHeight: '200',
            'concrete5': {
                filemanager: <?php echo $fp->canAccessFileManager(); ?>,
                sitemap: <?php echo $tp->canAccessSitemap(); ?>,
                lightbox: true
            }
        });
        $('.btn-add-item').click(function(){
            var temp = $(".items-container .item").length;
            temp = (temp);
            itemsContainer.append(itemTemplate({
                fID: '',
                thumb: '',
                headline: '',
                content: '',
                cID: '',
                pageName: '',
                button: '',
                sort: temp
            }));
            var thisModal = $(this).closest('.ui-dialog-content');
            var newItem = $('.items-container .item').last();
            thisModal.scrollTop(newItem.offset().top);
            newItem.find('.redactor-content').redactor({
                minHeight: '100',
                'concrete5': {
                    filemanager: <?php echo $fp->canAccessFileManager(); ?>,
                    sitemap: <?php echo $tp->canAccessSitemap(); ?>,
                    lightbox: true
                }
            });
            indexItems();
        })
    });
</script>