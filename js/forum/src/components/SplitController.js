export default class SplitController {
    constructor () {
        this._isSplitting = false;
    }

    start(postId, postNo, discussionId) {
        // should not be necessary
        if (postId == 1) return;

        this._startPost = postId;
        this._discussion = discussionId;
        this._isSplitting = true;

        $('.PostStream-item').each(function () {
            var postIndex = $(this).attr('data-number');
            if (postIndex > postNo) {
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

    log() {
        console.log('splitting:' + this._isSplitting);
        console.log('discussion:' + this._discussion);
        console.log('startPost:' + this._startPost);
        console.log('endPost:' + this._endPost);
    }
}
