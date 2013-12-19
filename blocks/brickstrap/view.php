<?php  defined('C5_EXECUTE') or die("Access Denied.");

Loader::model('attribute/categories/collection');

$ak = CollectionAttributeKey::getByHandle('tags');
$akc = $ak->getController();
$pp = false;

$taggery = $akc->getOptionUsageArray($pp);
$tags = array();

foreach($taggery as $tag){
	// $tags[] = '<li class="brand" data-tags="'.$tag.'" data-uses="'.$tag->getSelectAttributeOptionUsageCount().'">'.$tag.'</li>';
	$tags[] = $tag;
}

// shuffle($tags);

if (Page::getCurrentPage()->isEditMode()) echo '<div class="well">Content disabled in edit mode.</div>'; else { ?>

<div id="filter" class="wrap">

	<ol id="caller" class="caller">
		<li><button type="button" name="filter" class="btn btn-sm btn-black" value=".brick-sm">Thumbnail</button></li>
		<li><button type="button" name="filter" class="btn btn-sm btn-black" value=".brick-md">Text</button></li>
		<li><button type="button" name="filter" class="btn btn-sm btn-black" value=".brick-lg">Artwork</button></li><?php  

		for ($i = 0; $i < $taggery->count(); $i++) { if (($i+1) <= 4) { $akct = $tags[$i]; ?>

		<li><button type="button" name="filter" class="btn btn-sm btn-black" value="<?php  echo $akct; ?>"><?php  echo $akct; ?></button></li><?php 

		} } ?>

		<li><button type="button" name="filter" class="btn btn-sm btn-black" value="position">Position exceeds 5</button></li>
		<li><button type="button" name="filter" class="btn btn-sm btn-red" value="*">Reset</button></li>
	</ol>


	<div id="brickstrap" class="isotope">

		<div class="sizer"></div><?php  foreach ($bricks as $brick) {

		$target = Page::getByID($brick->cID);
		$keys = $target->getAttribute('tags');
		$position = $brick->position;
		if ($brick->title !== "") $title = $brick->title; else $title = $target->getCollectionName();
		$description = htmlentities($brick->description, ENT_QUOTES, APP_CHARSET);
		$link = $brick->url;
		$image = $brick->image;
		$thumbnail = $target->getBlocks('Thumbnail Image');

		?>

		<div class="brick <?php  if ($image) echo 'brick-lg'; elseif (is_object($thumbnail[0])) echo 'brick-sm'; else echo 'brick-md'; ?>" data-position="<?php
			echo $position; ?>" data-tags="<?php 

				// echo htmlentities($keys, ENT_QUOTES, APP_CHARSET);

				if (!empty($keys)) {
					$filters = array();
					foreach($keys as $opt) {
						// $filters[] = '<span>'.$opt.'</span>, ';
						$filters[] = $opt;
					}
					echo implode(', ',$filters);
				}


			?>"><!-- <?php
			echo t('Brick No. '); echo $position; ?> --><div>
			<?php  
			if ($image) { 
				$f = File::getByID($image);
				if ($link !== "") print '<a href="'.$link.'">';
				print '<img src="'.$f->getRelativePath().'" title="'.$title.'" alt="'.$f->getTitle().'">';
				if ($link !== "") echo '</a>';
			} elseif (is_object($thumbnail[0])) {
				$instance = $thumbnail[0]->getInstance();
				$thumb = $instance->getFileObject();
				if ($link !== "") print '<a href="'.$link.'">';
				if($thumb) print '<img src="'.$thumb->getRelativePath().'" alt="'.$thumb->getTitle().'">';
				if ($link !== "") echo '</a>';
			} // if (!$image && !is_object($thumbnail[0])) print '<p class="text-right"><a class="btn btn-xs" href="'.$link.'">Learn more</a></p>';

			if ($description !== "") { ?>

			<div>
				<h3><?php  if ($link !== "") print '<a href="'.$link.'">'; echo $title; if ($link !== "") print '</a>'; ?></h3>
				<p><?php  echo $description; ?></p>
			</div><?php  } ?>

		</div></div><?php  } ?>

	</div>

</div>

<?php  
// View::getInstance()->addFooterItem(Loader::helper('html')->javascript('brickstrap.js','brickstrap'));
// View::getInstance()->addFooterItem('');

print Loader::helper('html')->javascript('brickstrap.js','brickstrap'); ?>

<script>
	var $box = $("#brickstrap").imagesLoaded(function(){
		$box.isotope({
			masonry: {columnWidth: ".sizer"},
			itemSelector: ".brick",
			isJQueryFiltering: true,
			transitionDuration: "0.6s"
		});
	});
	var filterFns = {
		position:function(){var number=$(this).attr("data-position");return parseInt(number,10)>5},<?php  

		for ($i = 0; $i < $taggery->count(); $i++) { if (($i+1) <= 4) { $akct = $tags[$i]; ?>

		<?php  echo $akct; ?>:function(){var tag=$(this).attr("data-tags");return tag.match(/<?php  echo $akct; ?>/)},<?php  }} ?>

	};
	$("#caller").on("click","button", function() {
		var filtering = filterFns[this.value] || this.value;
		$box.isotope({filter:filtering});
	});

</script>

<?php  } ?>
