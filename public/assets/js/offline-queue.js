const QUEUE_KEY = 'absensi_offline_queue';

/**
 * SIMPAN KE QUEUE
 */
function saveOffline(data) {
  const queue = JSON.parse(localStorage.getItem(QUEUE_KEY) || '[]');
  queue.push({
    data,
    time: new Date().toISOString()
  });
  localStorage.setItem(QUEUE_KEY, JSON.stringify(queue));
}

/**
 * KIRIM QUEUE KE SERVER
 */
async function syncOfflineQueue() {
  const queue = JSON.parse(localStorage.getItem(QUEUE_KEY) || '[]');
  if (!queue.length) return;

  const newQueue = [];

  for (const item of queue) {
    try {
      const res = await fetch(item.data.url, {
        method: 'POST',
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: item.data.body
      });

      if (!res.ok) throw new Error('Failed');
    } catch (e) {
      newQueue.push(item);
    }
  }

  localStorage.setItem(QUEUE_KEY, JSON.stringify(newQueue));
}

/**
 * LISTENER ONLINE
 */
window.addEventListener('online', () => {
  syncOfflineQueue();
});

/**
 * EXPORT GLOBAL
 */
window.absensiOffline = {
  saveOffline,
  syncOfflineQueue
};
