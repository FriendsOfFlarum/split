import Discussion from 'flarum/common/models/Discussion';
import type SplitController from '../forum/SplitController';
import type ForumApplication from 'flarum/forum/ForumApplication';

declare module 'flarum/common/models/Discussion' {
  export default interface Discussion {
    canSplit(): boolean;
  }
}

declare module 'flarum/forum/ForumApplication' {
  export default interface ForumApplication {
    __fof_split: {
      splitting: boolean;
      showSplitTos: Record<string, any>;
      splitController: SplitController | null;
      splittingFrom?: number;
    };
  }
}

declare global {
  interface Window {
    app: ForumApplication & {
      __fof_split: {
        splitting: boolean;
        showSplitTos: Record<string, any>;
        splitController: SplitController | null;
        splittingFrom?: number;
      };
    };
  }
}
