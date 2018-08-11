export default class SplitController {
    constructor() {
        this.reset();
    }

    start(postId, postNumber) {
        this.reset();

        this.startPostId = postId;

        $('.PostStream-item').each(function () {
            if ($(this).attr('data-number') >= postNumber) {
                $('.flagrow-split-endSplitButton', $(this)).show();
            }
        });

        $('.flagrow-split-startSplitButton').hide();
    }

    end(postNumber) {
        this.endPostNumber = postNumber;
    }

    reset() {
        this.startPostId = null;
        this.endPostNumber = null;
    }
}
