<div class="card mb-3" id="{{componentId}}" style="max-width: 540px;">
    <div class="row g-0">
        <div class="col-md-4">
            <?php
            if (isset($source)) {
                echo '<img src= /assets/images/' . htmlspecialchars($source) . ' class="img-fluid rounded-start" alt="...">';
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
                    echo '<button type="button" class="btn-action">' . $button . '</button>';
                }
                if (isset($footer)) {
                    echo '
                    <p class="card-text">
                      <small class="text-body-secondary">
                    '
                        . $footer .
                        '
                      </small>
                    </p>
                ';
                }
                ?>
            </div>
        </div>
    </div>
</div>