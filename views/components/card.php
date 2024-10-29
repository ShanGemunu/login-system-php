<div class="card mb-3 card-div" id="{{componentId}}">
    <div class="row g-0 mh-100">
        <div class="col-md-4 image-div">
            <?php
            if (isset($source)) {
                echo '<img src= /assets/images/' . htmlspecialchars($source) . ' class="img-fluid rounded-start custom-image" alt="...">';
            } else {
                echo '<img src="" alt="no image">';
            }
            ?>
        </div>
        <div class="col-md-8">
            <div class="card-body">
                <h5 class="card-title">
                    <?php
                    if (isset($title)) {
                        echo $title;
                    } else {
                        echo "no title";
                    }
                    ?>
                </h5>
                <p class="card-text">
                    <?php
                    if (isset($body)) {
                        echo $body;
                    } else {
                        echo "no body";
                    }
                    ?>
                </p>
                <?php
                if (isset($button)) {
                    echo "<button type='button' class='{$button['className']} btn btn-outline-primary btn-sm me-3 btn-w'> {$button['text']} </button>";
                }
                if (isset($incButton)) {
                    echo "<button type='button' class='{$incButton['className']} btn btn-outline-primary btn-sm me-3 btn-w'> {$incButton['text']} </button>";
                }
                if (isset($subButton)) {
                    echo "<button type='button' class='{$subButton['className']} btn btn-outline-primary btn-sm me-3 btn-w'> {$subButton['text']} </button>";
                }
                if (isset($removeButton)) {
                    echo "<button type='button' class='{$removeButton['className']} btn btn-outline-primary btn-sm me-3 btn-w'> {$removeButton['text']} </button>";
                }
                if (isset($footer)) {
                    echo "
                    <p class='card-text'>
                      <small class='text-body-secondary footer-cla'>
                    
                        {$footer} 
                        
                      </small>
                    </p>
                ";
                }
                ?>
            </div>
        </div>
    </div>
</div>