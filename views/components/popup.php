<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
    <?php
    if (isset($triggerButton)) {
        echo $triggerButton;
    }else{
        echo "Cilck";
    }
    ?>
</button>

<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">
                    <?php
                    if (isset($title)) {
                        echo $title;
                    }else{
                        echo "Alert";
                    }
                    ?>
                </h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <?php
                if (isset($body)) {
                    echo $body;
                }else{
                    echo "Nothing";
                }
                ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <?php
                    if (isset($closeButton)) {
                        echo $closeButton;
                    }else{
                        echo "close";
                    }
                    ?>
                </button>
                <button type="button" class="btn btn-primary">
                    <?php
                    if (isset($successButton)) {
                        echo $successButton;
                    }else{
                        echo "ok";
                    }
                    ?>
                </button>
            </div>
        </div>
    </div>
</div>