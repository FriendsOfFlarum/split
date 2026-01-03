export default class SplitController {
    startPostId: string | null;
    endPostNumber: number | null;
    constructor();
    start(postId: string, postNumber: number): void;
    end(postNumber: number): void;
    reset(): void;
}
