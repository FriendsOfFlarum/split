import app from 'flarum/forum/app';

export default class SplitController {
  startPostId: string | null = null;
  endPostNumber: number | null = null;

  constructor() {
    this.reset();
  }

  start(postId: string, postNumber: number): void {
    this.reset();

    this.startPostId = postId;

    app.__fof_split.splitting = true;
    app.__fof_split.splittingFrom = postNumber;

    m.redraw();
  }

  end(postNumber: number): void {
    this.endPostNumber = postNumber;

    app.__fof_split.splitting = false;
  }

  reset(): void {
    this.startPostId = null;
    this.endPostNumber = null;

    app.__fof_split.splitting = false;
    app.__fof_split.splittingFrom = undefined;
  }
}
