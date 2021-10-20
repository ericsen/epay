
/**
 * -------------------------------------------------------------------
 *  前端推播通知 Client Site (前端交易員使用，先暫時放在這邊)
 * -------------------------------------------------------------------
 */

// 接收交易單撮合成功通知 (前端交易員使用，先暫時放在這邊)
// window.Echo.private(`order.trader.${user.id}`).listen(
//     '.push_order_to_trader',
//     data => {
//         console.info(data);
//     }
// );
var order_vm = new Vue({
    channel: `private:order.trader.${user.id}`,
    echo: {
        '.push_order_to_trader': (payload, order_vm) => {
            console.log('trader order pushed', payload);
        },
    }
});

// 接收系統訊息 (前端交易員使用，先暫時放在這邊)
// window.Echo.channel('system_channel.news').listen(
//     '.push_system_news_to_everyone',
//     data => {
//         console.info(data);
//     }
// );
var news_vm = new Vue({
    channel: 'system.news',
    echo: {
        '.push_system_news_to_everyone': (payload, news_vm) => {
            console.log('system news pushed', payload);
        },
    }
});


// 回報交易員儲值單狀態
var payment_vm = new Vue({
    channel: `private:payment.trader.${user.id}`,
    echo: {
        '.push_payment_status_to_trader': (payload, payment_vm) => {
            console.log('trader payment status pushed', payload);
        },
    }
});


/**
 * -------------------------------------------------------------------
 *  後端推播通知
 * -------------------------------------------------------------------
 */

// 接收交易員申訴通知
// window.Echo.channel('order_channel.trader.feedback').listen(
//     '.push_trader_feedback',
//     data => {
//         console.info(data);
//     }
// );
var feedback_vm = new Vue({
    el: '#feedback',
    channel: 'order.trader.feedback',
    echo: {
        '.push_trader_feedback': (payload, feedback_vm) => {
            console.log('feedback post created', payload);
        },
    }
});


// 接收交易員申請驗證通知
var verify_vm = new Vue({
    channel: 'trader.verify_request',
    echo: {
        '.trader_verify_request': (payload, verify_vm) => {
            console.log('trader verify request', payload);
        },
    }
});



// 回報交易員儲值單狀態
var trader_payment_vm = new Vue({
    channel: `trader.payment`,
    echo: {
        '.push_trader_payment_order_to_backend': (payload, trader_payment_vm) => {
            console.log('trader payment order created pushed', payload);
        },
    }
});

// 接收交易員等候過期通知
var pool_vm = new Vue({
    channel: `private:${window.pool.channels.expired}.${user.id}`,
    echo: {
        ['.'+window.pool.events.expired] : (payload, pool_vm) => {
            console.log('pool request', payload);
        },
    }
});
