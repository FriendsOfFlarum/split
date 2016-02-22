class SplitController {
    constructor () {
        this.isSplitting = false;
    }

    set start(postId) {
        this.startPost = postId;
    }

    set end(postId) {
        this.endPost = postId;
    }

    get start() {
        return this.startPost;
    }

    get end() {
        return this.endPost;
    }

    static log() {
        console.log('splitting:'.this.isSplitting);
        console.log('startPost:'.this.startPost);
        console.log('endPost:'.this.endPost);
    }
}