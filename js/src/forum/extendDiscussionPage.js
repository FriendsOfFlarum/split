import { extend } from 'flarum/common/extend';
import DiscussionPage from 'flarum/forum/components/DiscussionPage';

export default function () {
  extend(DiscussionPage.prototype, 'oninit', function () {
    this.splitting = false;
  });
}
