<?php namespace Concrete\Package\Brickstrap;

use Package;
use BlockType;
use Asset;
use AssetList;
use Loader;
class Controller extends Package {

    protected $pkgHandle = 'brickstrap';
    protected $appVersionRequired = '5.7.1';
    protected $pkgVersion = '1.0.1';

    public function getPackageName() {
        return t("Brickstrap");
    }

    public function getPackageDescription() {
        return t("Package powers magical grid.");
    }
	
    public function install() {
        $pkg = parent::install();
        $this->addBlocks($pkg);
    }

    public function uninstall() {
        parent::uninstall();
        Loader::db()->execute('DROP TABLE btBrickstrap'); // Loader::db()->Execute('DROP TABLE btBrickstrap, btBrick');
    }

    private function addBlocks($pkg) {
        BlockType::installBlockTypeFromPackage('brickstrap', $pkg);
    }

    public function on_start() {
        $al = AssetList::getInstance();
        $al->register('javascript', 'brickstrap', 'js/brickstrap.js', array(
            'version' => '1.0',
            'position' => Asset::ASSET_POSITION_FOOTER,
            'minify' => false,
            'combine' => false
        ), $this);
    }

}