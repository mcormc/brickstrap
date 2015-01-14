<?php defined('C5_EXECUTE') or die(_("Access Denied.")); if (Page::getCurrentPage()->isEditMode()) echo '<div class="ccm-edit-mode-disabled-item">'.t('DISABLED').'</div>'; else {

$ih = Core::make('helper/image');
$nh = Core::make('helper/navigation');
$c = Page::getCurrentPage();

$ak = CollectionAttributeKey::getByHandle('tags');
$akc = $ak->getController();
$pp = false;
$tags = $akc->getOptionUsageArray($pp); ?>



        <div class="container">
            <div class="page-header"><h2><?php echo $title; ?></h2></div>
            <div id="BRICKSTRAP<?php echo $bID; ?>" class="brickstrap">
                <ul id="FILTER<?php echo $bID; ?>" class="filter">
                    <li><button type="button" name="filter" class="btn btn-xs btn-default" value=".brick-md">Text</button></li>
                    <li><button type="button" name="filter" class="btn btn-xs btn-default" value=".brick-lg">Artwork</button></li><?php 

                    foreach($tags as $t) { ?>

                    <li><button type="button" name="filter" class="btn btn-xs btn-default" data-id="<?php
                                echo $t->getSelectAttributeOptionID(); // $akc->field('atSelectOptionID'); ?>" data-name="<?php
                                echo $t->getSelectAttributeOptionValue(); ?>" data-uses="<?php
                                echo $t->getSelectAttributeOptionUsageCount(); ?>"><?php
                                echo $t->getSelectAttributeOptionValue(); ?></button></li><?php 

                    } ?>

                </ul>
                <div id="ISOTOPE<?php echo $bID; ?>" class="isotope">
                    <div class="sizer"></div><?php foreach ($items as $item) {

                    $target = Page::getByID($item['cID']);
                    $keys = $target->getAttribute('tags');
                    $position = $item['sort'];
                    if ($item->headline !== "") $headline = $item['headline']; else $headline = $target->getCollectionName();
                    $description = strip_tags($item['content']); // htmlentities($item['content'], ENT_QUOTES, APP_CHARSET);
                    $link = Page::getCollectionPathFromID($item['cID']);
                    $image = $item['fID'];
                    // $thumbnail = $target->getBlocks('Thumbnail Image'); ?>

                    <div class="brick <?php 
                        // if ($image) echo 'brick-lg'; else echo 'brick-md';
                        if ($position == 1) echo 'brick-lg'; else echo 'brick-md'; ?>" data-position="<?php echo $position; ?>" data-tags="<?php 
                                    if (!empty($keys)) {
                                        $filters = array();
                                        foreach($keys as $opt) $filters[] = $opt;
                                        echo implode(', ',$filters);
                                    } ?>"><!-- <?php echo t('Brick No. '); echo $position; ?> --><div>
                        <?php

                        if ($image) { 
                            $f = File::getByID($image);
                            if ($link !== "") print '<a href="'.$link.'">';
                            print '<img src="'.$f->getRelativePath().'" title="'.$title.'" alt="'.$f->getTitle().'">';
                            if ($link !== "") echo '</a>';
                        }

                        if ($description !== "") { ?>

                        <div>
                            <h3><?php if ($link !== "") print '<a href="'.$link.'">'; echo $title; if ($link !== "") print '</a>'; ?></h3>
                            <p><?php echo $description; ?></p>
                        </div><?php

                        } ?>

                    </div></div><?php

                    } ?>

                </div>
            </div>

            <script>
                $(function(){
                    var $box = $(".isotope").imagesLoaded(function(){
                        $box.isotope({
                            masonry: {columnWidth: ".sizer"},
                            itemSelector: ".brick",
                            isJQueryFiltering: true,
                            transitionDuration: "0.6s"
                        });
                    });
                    var tags = {
                        position:function(){var number=$(this).attr("data-position");return parseInt(number,10)>5},<?php 

                        foreach($tags as $t) { ?>

                        <?php echo $t->getSelectAttributeOptionValue(); ?>:function(){var tag=$(this).attr("data-tags");return tag.match(/<?php
                                    echo $t->getSelectAttributeOptionValue(); ?>/)},<?php 

                        } ?>

                    };
                    $(".filter").click(function() { // $(".filter").on("click", "button", function() {
                        var filters = tags[this.value] || this.value;
                        $box.isotope({filter:filters});
                    })
                });
            </script>
        </div><?php

} ?>
