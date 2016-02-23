export default class SplitController {
    constructor () {
        this.isSplitting = false;
    }

    set start(postId) {
        this.startPost = postId;
        this.isSplitting = true;
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

    log() {
        console.log('splitting:' + this.isSplitting);
        console.log('startPost:' + this.startPost);
        console.log('endPost:' + this.endPost);
    }
}