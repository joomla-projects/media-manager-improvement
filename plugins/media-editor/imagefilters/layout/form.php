<?php
$name = JFactory::getApplication()->input->getCmd('name');
$filePath = $displayData['filePath'];

JHtml::_('jquery.framework');
JFactory::getDocument()->addScript('/media/plg_media-editor_imagefilters/js/caman.full.min.js');
JFactory::getDocument()->addScript('/media/plg_media-editor_imagefilters/js/caman-init.js');
JFactory::getDocument()->addStyleSheet('/media/plg_media-editor_imagefilters/css/caman.css');

$image = COM_MEDIA_BASEURL . '/' . $filePath;

$path_parts = pathinfo($filePath);
$folder     =$path_parts['dirname'];

$session    = JFactory::getSession();
$uploadUrl  = JUri::base() . 'index.php?option=com_media&task=file.upload&tmpl=component&allowovewrite=true&folder='
    . $folder . '&'
    . $session->getName() . '=' . $session->getId()
    . '&' . JSession::getFormToken() . '=1'
    . '&asset=image&format=json';
?>
<div class="btn-toolbar imagefilters-toolbar">


    <span id="joomla-media-image-filters" src="<?php echo $image; ?>" class="hidden" <?php echo 'data-url="'. $uploadUrl . '"'; ?>></span>
    <canvas id="filter-canvas" style="left:auto; right:auto;"></canvas>

    <div class="preset-filters" class="span12" >
        <h3>Presets (Instagram)</h3>
        <a class="btn btn-primary" data-preset="vintage">Vintage</a>
        <a class="btn btn-primary" data-preset="lomo">Lomo</a>
        <a class="btn btn-primary" data-preset="clarity">Clarity</a>
        <a class="btn btn-primary" data-preset="sinCity">Sin City</a>
        <a class="btn btn-primary" data-preset="sunrise">Sunrise</a>
        <a class="btn btn-primary" data-preset="crossProcess">Cross Process</a>
        <a class="btn btn-primary" data-preset="orangePeel">Orange Peel</a>
        <a class="btn btn-primary" data-preset="love">Love</a>
        <a class="btn btn-primary" data-preset="grungy">Grungy</a>
        <a class="btn btn-primary" data-preset="jarques">Jarques</a>
        <a class="btn btn-primary" data-preset="pinhole">Pinhole</a>
        <a class="btn btn-primary" data-preset="oldBoot">Old Boot</a>
        <a class="btn btn-primary" data-preset="glowingSun">Glowing Sun</a>
        <a class="btn btn-primary" data-preset="hazyDays">Hazy Days</a>
        <a class="btn btn-primary" data-preset="herMajesty">Her Majesty</a>
        <a class="btn btn-primary" data-preset="nostalgia">Nostalgia</a>
        <a class="btn btn-primary" data-preset="hemingway">Hemingway</a>
        <a class="btn btn-primary" data-preset="concentrate">Concentrate</a>

        <a class="btn btn-warning" data-preset="reset">Reset</a>

        <a class="btn btn-success" data-preset="save">Save changes</a>
    </div>

    <div id="Filters" class="hidden">

        <div class="span6">
            <div class="Filter">
                <div class="FilterName">
                    <p>brightness</p>
                </div>

                <div class="FilterSetting">
                    <input type="range" min="-100" max="100" step="1" value="0" data-filter="brightness">
                    <span class="FilterValue">0</span>
                </div>
            </div>

            <div class="Filter">
                <div class="FilterName">
                    <p>contrast</p>
                </div>

                <div class="FilterSetting">
                    <input type="range" min="-100" max="100" step="1" value="0" data-filter="contrast">
                    <span class="FilterValue">0</span>
                </div>
            </div>

            <div class="Filter">
                <div class="FilterName">
                    <p>saturation</p>
                </div>

                <div class="FilterSetting">
                    <input type="range" min="-100" max="100" step="1" value="0" data-filter="saturation">
                    <span class="FilterValue">0</span>
                </div>
            </div>

            <div class="Filter">
                <div class="FilterName">
                    <p>vibrance</p>
                </div>

                <div class="FilterSetting">
                    <input type="range" min="-100" max="100" step="1" value="0" data-filter="vibrance">
                    <span class="FilterValue">0</span>
                </div>
            </div>

            <div class="Filter">
                <div class="FilterName">
                    <p>exposure</p>
                </div>

                <div class="FilterSetting">
                    <input type="range" min="-100" max="100" step="1" value="0" data-filter="exposure">
                    <span class="FilterValue">0</span>
                </div>
            </div>

            <div class="Filter">
                <div class="FilterName">
                    <p>hue</p>
                </div>

                <div class="FilterSetting">
                    <input type="range" min="0" max="100" step="1" value="0" data-filter="hue">
                    <span class="FilterValue">0</span>
                </div>
            </div>
        </div>

        <div class="span6">

            <div class="Filter">
                <div class="FilterName">
                    <p>sepia</p>
                </div>

                <div class="FilterSetting">
                    <input type="range" min="0" max="100" step="1" value="0" data-filter="sepia">
                    <span class="FilterValue">0</span>
                </div>
            </div>

            <div class="Filter">
                <div class="FilterName">
                    <p>gamma</p>
                </div>

                <div class="FilterSetting">
                    <input type="range" min="0" max="10" step="0.1" value="0" data-filter="gamma">
                    <span class="FilterValue">0</span>
                </div>
            </div>

            <div class="Filter">
                <div class="FilterName">
                    <p>noise</p>
                </div>

                <div class="FilterSetting">
                    <input type="range" min="0" max="100" step="1" value="0" data-filter="noise">
                    <span class="FilterValue">0</span>
                </div>
            </div>

            <div class="Filter">
                <div class="FilterName">
                    <p>clip</p>
                </div>

                <div class="FilterSetting">
                    <input type="range" min="0" max="100" step="1" value="0" data-filter="clip">
                    <span class="FilterValue">0</span>
                </div>
            </div>

            <div class="Filter">
                <div class="FilterName">
                    <p>sharpen</p>
                </div>

                <div class="FilterSetting">
                    <input type="range" min="0" max="100" step="1" value="0" data-filter="sharpen">
                    <span class="FilterValue">0</span>
                </div>
            </div>

            <div class="Filter">
                <div class="FilterName">
                    <p>stackBlur</p>
                </div>

                <div class="FilterSetting">
                    <input type="range" min="0" max="20" step="1" value="0" data-filter="stackBlur">
                    <span class="FilterValue">0</span>
                </div>
            </div>
        </div>

    </div>
</div>

