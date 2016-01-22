import { extend } from 'flarum/extend';
import DiscussionPage from 'flarum/components/DiscussionPage';
import Splitter from 'flarum/utils/SplitController';

export default function() {
    extend(DiscussionPage, 'init', function() {
        this.splitter = new Splitter;
    });
}