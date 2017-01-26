export default class SplitController {
    constructor () {
        this._isSplitting = false;
    }

    start(postId, postNo) {
        // should not be necessary
        if (postNo == 1) return;

        this._startId = postId;
        this._startPost = postNo;
        this._endPost = null;

        $('.PostStream-item').each(function () {
            var postIndex = $(this).attr('data-number');
            if (postIndex >= this._startPost) {
                $('.flagrow-split-endSplitButton', $(this)).show();
            }
        });
        $('.flagrow-split-startSplitButton').hide();

    }

    end(postNo) {
        this._endPost = postNo;
    }

    startPost() {
        return this._startPost;
    }

    endPost() {
        return this._endPost;
    }

    reset() {
        this._isSplitting = false;
        this._startPost = null;
        this._endPost = null;
    }
}
