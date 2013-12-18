<?php  defined('C5_EXECUTE') or die("Access Denied."); $ah = Loader::helper('concrete/interface'); ?>


<style>

</style>

<div style="text-align: right"><a id="ccm-edit-addRow" class="btn btn-inverse" dialog-title="<?php  echo t('Choose Page'); ?>" href="<?php  
	echo REL_DIR_FILES_TOOLS_REQUIRED; ?>/sitemap_search_selector.php?sitemap_select_mode=select_page&callback=GridSitemapNode"><?php  echo t('Add brick');
?></a></div><hr />

<div id="ccm-edit-rows">
<?php  foreach($items as $rowInfo) {

	$rowInfo['rowID'] = $rowInfo['position'];
	$p = Page::getByID($rowInfo['targetID']);
	$rowInfo['title'] = $p->getCollectionName();

	$this->inc('edit_row.php', array('rowInfo' => $rowInfo));
} ?>
</div>

<div id="rowTemplateWrap" style="display:none">
<?php 
$tmpRowInfo = array(
	'rowID' => 'tempRowID',
	'targetID' => 'tempTargetID',
	'title' => 'tempTitle'
);
$this->inc('edit_row.php', array('rowInfo' => $tmpRowInfo));
?>
</div>

<script type="text/javascript">
	$(document).ready(function() {
		$('#ccm-edit-addRow').unbind();
		$('#ccm-edit-addRow').dialog();
	});
</script>
<hr />