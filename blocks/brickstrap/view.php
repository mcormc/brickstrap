<?php  defined('C5_EXECUTE') or die("Access Denied.");

$ak = CollectionAttributeKey::getByHandle('tags');
$akc = $ak->getController();
$pp = false;

$tagCounts = array();

if ($baseSearchPath != '') $pp = Page::getByPath($baseSearchPath);

$bricklike = $akc->getOptionUsageArray($pp);
$bricklike_tags = array();
foreach($bricklike as $bl){
	$tagCounts[] = $bl->getSelectAttributeOptionUsageCount();
	$bricklike_tags[] = '<li class="brand" data-tags="'.$bl.'" data-uses="'.$bl->getSelectAttributeOptionUsageCount().'">'.$bl.'</li>';
}
// shuffle($bricklike_tags); ?>

<div class="what">
<h1>Brickstrap, a prototype</h1>
<p>Magical grid lets user customize navigation by filtering through responsive bricks, each of which promotes page and houses content, such as art and copy, selected by administrator during implementation and revision of block's instance. Package for concrete5's consideration runs on <a href="http://isotope.metafizzy.co/">Isotope</a>, <a href="http://masonry.desandro.com/">Masonry</a>, and <a href="http://getbootstrap.com/">Bootstrap 3</a>.</p>
</div>

<div id="filter" class="wrap">


	<ol id="caller" class="caller">
		<li><button type="button" name="filter" class="btn btn-info" value=".brick-sm">Thumbnail</button></li>
		<li><button type="button" name="filter" class="btn btn-info" value=".brick-md">Text</button></li>
		<li><button type="button" name="filter" class="btn btn-info" value=".brick-lg">Artwork</button></li>
		<li><button type="button" name="filter" class="btn btn-primary" value="indoor">Inside</button></li>
		<li><button type="button" name="filter" class="btn btn-primary" value="outdoor">Outside</button></li>
		<li><button type="button" name="filter" class="btn btn-primary" value="critter">Animal</button></li>
		<li><button type="button" name="filter" class="btn btn-warning" value="position">Position exceeds 5</button></li>
		<li><button type="button" name="filter" class="btn btn-success" value="*">Reset</button></li>
	</ol>


	<div id="isotope" class="isotope">

		<div class="sizer"></div><?php  foreach ($items as $item) {

		$target = Page::getByID($item->cID);
		$keys = $target->getAttribute('tags');
		$position = $item->position;
		$headline = $item->headline;
		$description = htmlentities($item->subhead, ENT_QUOTES, APP_CHARSET);
		$link = $item->url;
		$thumbnail = $target->getBlocks('Thumbnail Image');

		?>

		<div class="brick <?php  if ($item->imageID) echo 'brick-lg'; elseif (is_object($thumbnail[0])) echo 'brick-sm'; else echo 'brick-md'; ?>" data-position="<?php
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
			echo t('Brick No. '); echo $item->position; ?> --><div>

			<?php  
			if ($item->imageID) { 
				$f = File::getByID($item->imageID);
				print '<img src="'.$f->getRelativePath().'" title="'.$f->getTitle().'" alt="'.$f->getTitle().'">';
			} elseif (is_object($thumbnail[0])) {
				$instance = $thumbnail[0]->getInstance();
				$thumb = $instance->getFileObject();
				if($thumb) print '<img src="'.$thumb->getRelativePath().'" alt="'.$thumb->getTitle().'">';
			} // if (!$item->imageID && !is_object($thumbnail[0])) print '<p class="text-right"><a class="btn btn-xs" href="'.$item->url.'">Learn more</a></p>';

			if ($headline !== "" || $description !== "") { ?>

			<div><?php  if ($headline !== "") { ?>

				<h3><?php  if ($link !== "") print '<a href="'.$link.'">'; echo $headline; if ($link !== "") print '</a>'; ?></h3><?php  } 
				if ($description !== "") { ?>

				<p><?php  echo $description; ?></p><?php  } ?>

			</div><?php  } ?>

		</div></div><?php  } ?>

	</div>

</div>

<script src="<?php  echo DIR_REL.'/js/imagesloaded.js'; ?>"></script>
<script src="<?php  echo DIR_REL.'/js/isotope.js'; ?>"></script>

<script>

	var $container = $("#isotope").imagesLoaded(function(){
		$container.isotope({
			masonry: {columnWidth: ".sizer"},
			itemSelector: ".brick",
			isJQueryFiltering: true,
			transitionDuration: "0.6s"
		});
	});

	var filterFns = {
		position: function() {
			var name = $(this).attr("data-position");
			return parseInt(name,10) > 5;
		},
		indoor: function() {
			var name = $(this).attr("data-tags");
			return name.match(/inside/); // show if tags include "outside"
		},
		outdoor: function() {
			var name = $(this).attr("data-tags");
			return name.match(/outside/); // show if tags include "outside"
		},
		critter: function() {
			var name = $(this).attr("data-tags");
			return name.match(/animal/); // show if tags include "animal"
		}
	};


	$("#caller").on("click","button", function() {
		var filtering = filterFns[this.value] || this.value;
		$container.isotope({filter:filtering});
	});

</script>