export default class SplitController {
    constructor () {
        this._isSplitting = false;
    }

    start(postId, discussionId) {
        // should not be necessary
        if (postId == 1) return;

        this._startPost = postId;
        this._discussion = discussionId;
        this._isSplitting = true;

        $('.PostStream-item').each(function () {
            var postIndex = $(this).attr('data-number');
            if (postIndex > postId) {
                $('.flagrow-split-endSplitButton', $(this)).show();
            }
        });
        $('.flagrow-split-startSplitButton').hide();

    }

    end(postId) {
        this._endPost = postId;
    }

    startPost() {
        return this._startPost;
    }

    endPost() {
        return this._endPost;
    }

    log() {
        console.log('splitting:' + this._isSplitting);
        console.log('discussion:' + this._discussion);
        console.log('startPost:' + this._startPost);
        console.log('endPost:' + this._endPost);
    }
}