jQuery(document).ready(function () {
    var textarea = jQuery("#markdownContent");

    jQuery(textarea).focus(function () {

        var $this = jQuery(this);
        $this.select();

        // Work around Chrome's little problem
        $this.mouseup(function () {
            // Prevent further mouseup intervention
            $this.unbind("mouseup");
            return false;
        });
    });
})