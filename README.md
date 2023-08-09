# FreshRSS - GReader Redate extension

This FreshRSS extension allows you to change the dates used by FreshRSS GReader API.

## Context

GReader API inherits from Google Reader (now abandoned). Google never wrote official documentation for it, so there is only unofficial documentation coming from reverse engineering. Here is a quick overview of the different dates of the stream subsystem of this API:
- `crawlTimeMsec` in µs — timestamp of the moment the feed was crawled by the aggregator.
- `timestampUsec` in ms — timestamp of the moment the item appeared in the original feed.
- `published` in s — timestamp of the moment the item was published in the original feed.

From its side, FreshRSS define those fields this way:
- `crawlTimeMsec` & `timestampUsec` — timestamp of the moment FreshRSS fetched the items (the one being just at different accuracy than the other).
- `published` — timestamp of the moment the items were published, as declared by the original feed.

`crawlTimeMsec` & `timestampUsec` are themselves tricked, so they are ordered (at the µs) in the same order as publishing date.

It happens that some RSS clients compatible with GReader API, like [EasyRSS](https://github.com/Alkarex/EasyRSS) or [Reeder](https://reederapp.com) or [FeedMe](https://github.com/seazon/FeedMe), are extracting only the `timestampUsec` field, and use it to order articles and date them in their user interface.

It can be unfortunate, because at the moment you subscribe to a new feed, all articles show almost the same date (and appear in what look like a random order in Reeder), as all articles are fetched almost at the same time.

If using `timestampUsec` can make sense for ordering, as the publishing date is not always accurate (depending of the feed), it can be a better user experience to have the real publishing dates in the user interface, and not the date of when FreshRSS fetched the articles.

As a workaround to this situation, this extension force FreshRSS to use the publishing date for `crawlTimeMsec` and `timestampUsec`, instead of the fetch date. By doing that, those RSS clients can show the published dates of the articles.

However, some care should be taken depending of your RSS client. The GReader API stream subsystem use timestamps to select which articles it wants to fetch. And if your RSS client use use `crawlTimeMsec` or `timestampUsec` to make its query, the accuracy can be not enough, and it would be possible to miss articles.

To be noted that at this date, Reeder don't use these values for its query, and just fetch articles since the last 2 weeks before the current date, so it doesn't change anything for it.


## Installation

To use this extension, you need to copy the `xExtension-GReaderRedate` directory in the `extensions` directory at the root of FreshRSS installation, and activate it in Extensions panel of the web interface.


## Changelog

1.0:
* Initial release.

1.1:
* Added support for Feedme.

1.2:
* Fix support for Feedme.
