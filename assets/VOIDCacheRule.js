'use strict';

// 若此文件发生变更，可通过变更版本号进行更新
const version = 'moewah_20210803';
const __DEVELOPMENT__ = false;
const __DEBUG__ = false;

// 默认缓存的静态资源，一般用于离线使用
const offlineResources = [
    '/',
    '/default.jpg',
    '/offline.html',
];

// 缓存忽略列表
const ignoreCache = [
	"/admin"
];

// 慎重使用全局可变变量，因为 serviceWork 不可控的停止和重启，会导致它们的取值在后续读取时无法预测
let port;


/**
 * common function
 */

function developmentMode() {
    return __DEVELOPMENT__ || __DEBUG__;
}

function cacheKey() {
    return [version, ...arguments].join(':');
}

function log() {
    if (developmentMode()) {
        console.log("SW:", ...arguments);
    }
}

// 不需要缓存的请求
function shouldAlwaysFetch(request) {
    return __DEVELOPMENT__ ||
        request.method !== 'GET' ||
        ignoreCache.some(regex => request.url.match(regex));
}

// 缓存 html 页面
function shouldFetchAndCache(request) {
    return (/text\/html/i).test(request.headers.get('Accept'));
}

/**
 * Install 安装
 */

function onInstall(event) {
    log('install event in progress.');

    event.waitUntil(
        caches.open(cacheKey('offline'))
            .then(cache => cache.addAll(offlineResources))
            .then(() => log('installation complete! version: ' + version))
            .then(() => self.skipWaiting())
    );
}

/**
 * Fetch
 */

// 当网络离线或请求发生了错误，使用离线资源替代 request 请求
function offlineResponse(request) {
    log('(offline)', request.method, request.url);
    if (request.url.match(/\.(jpg|png|gif|svg|jpeg)(\?.*)?$/)) {
        return caches.match('/default.jpg');
    } else {
        return caches.match('/offline.html');
    }
}

// 从缓存读取或使用离线资源替代
function cachedOrOffline(request) {
    return caches
        .match(request)
        .then((response) => response || offlineResponse(request));
}

// 从网络请求，并将请求成功的资源缓存
function networkedAndCache(request) {
    return fetch(request)
        .then(response => {
            if (!response.url || !response.ok) return response;

            const copy = response.clone();

            caches.open(cacheKey('resources'))
                .then(cache => {
                    cache.put(request, copy);
                });

            log("(network: cache write)", request.method, request.url);
            return response;
        });
}

// 优先从 cache 读取，读取失败则从网络请求并缓存。网络请求也失败，则使用离线资源替代
function cachedOrNetworked(request) {
    return caches.match(request)
        .then((response) => {
            log(response ? '(cached)' : '(network: cache miss)', request.method, request.url);
            return response ||
                networkedAndCache(request)
                .catch(() => offlineResponse(request));
        });
}

// 优先从网络请求，失败则使用离线资源替代
function networkedOrOffline(request) {
    return fetch(request)
        .then(response => {
            log('(network)', request.method, request.url);
            return response;
        })
        .catch(() => offlineResponse(request));
}

function onFetch(event) {
    const request = event.request;

    // 应当永远从网络请求的资源
    // 如果请求失败，则使用离线资源替代
    if (shouldAlwaysFetch(request)) {
        log('AlwaysFetch request: ', event.request.url);
        event.respondWith(networkedOrOffline(request));
        return;
    }

    // 应当从网络请求并缓存的资源
    // 如果请求失败，则尝试从缓存读取，读取失败则使用离线资源替代
    if (shouldFetchAndCache(request)) {
        event.respondWith(
            networkedAndCache(request).catch(() => cachedOrOffline(request))
        );
        return;
    }

    event.respondWith(cachedOrNetworked(request));
}

/**
 * Activate
 */

function removeOldCache() {
    return caches
        .keys()
        .then(keys =>
            Promise.all( // 等待所有旧的资源都清理完成
                keys
                .filter(key => !key.startsWith(version)) // 过滤不需要删除的资源
                .map(key => caches.delete(key)) // 删除旧版本资源，返回为 Promise 对象
            )
        )
        .then(() => {
            log('removeOldCache completed.');
        });
}

function onActivate(event) {
    log('activate event in progress.');
    event.waitUntil(Promise.all([
        // 更新客户端
        self.clients.claim(),
        removeOldCache()
    ]))
}

log("Hello from ServiceWorker land!", version);

self.addEventListener('install', onInstall);
self.addEventListener('fetch', onFetch);
self.addEventListener("activate", onActivate);

