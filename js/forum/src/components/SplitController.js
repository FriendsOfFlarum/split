export default class SplitController {
    start(postNo) {
        this.reset();

        this.startPost = postNo;

        $('.PostStream-item').each(function () {
            if ($(this).attr('data-number') >= postNo) {
                $('.flagrow-split-endSplitButton', $(this)).show();
            }
        });

        $('.flagrow-split-startSplitButton').hide();
    }

    end(postNo) {
        this.endPost = postNo;
    }

    startPost() {
        return this.startPost;
    }

    endPost() {
        return this.endPost;
    }

    reset() {
        this.startPost = null;
        this.endPost = null;
    }
}
