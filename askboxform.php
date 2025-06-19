    
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <div class="form-group">
            <label><b>Ask Me A Question</b></label><br />
            <input style="width:50%;height:70px;border-radius: 5px;" type="text" name="question" class="form-control" required>
        </div>
        <div class="form-group">
            <input type="submit" class="cutiepie" value="Submit" style="width:70px !important;margin-top:20px;z-index:9999;">
        </div>
    </form>
