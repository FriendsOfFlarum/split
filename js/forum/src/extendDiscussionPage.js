import { extend } from 'flarum/extend';
import DiscussionPage from 'flarum/components/DiscussionPage';

export default function() {
    extend(DiscussionPage.prototype, 'init', function() {
        this.splitting = false;
    });
}
