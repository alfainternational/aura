import { sleep } from 'k6';
import { check, group } from 'k6';
import { Rate, Trend, Counter } from 'k6/metrics';
import { config } from './config.js';
import { 
  users, 
  login, 
  createConversation, 
  sendMessage, 
  getConversations, 
  getMessages, 
  updateMessageStatus 
} from './helpers.js';

// تعريف المقاييس المخصصة
const sendMessageSuccess = new Rate('send_message_success');
const getMessagesSuccess = new Rate('get_messages_success');
const createConversationSuccess = new Rate('create_conversation_success');
const sendMessageDuration = new Trend('send_message_duration');
const getMessagesDuration = new Trend('get_messages_duration');
const failedRequests = new Counter('failed_requests');

// إعداد اختبار الأداء
export const options = {
  scenarios: {
    messaging_load: {
      executor: 'ramping-vus',
      startVUs: 5,
      stages: [
        { duration: '30s', target: 20 },
        { duration: '1m', target: 20 },
        { duration: '30s', target: 50 },
        { duration: '1m', target: 50 },
        { duration: '30s', target: 5 },
      ],
    },
  },
  thresholds: {
    'send_message_success': ['rate>0.95'],
    'get_messages_success': ['rate>0.95'],
    'create_conversation_success': ['rate>0.95'],
    'send_message_duration': [`p(95)<${config.thresholds.responseTime.p95}`, `p(99)<${config.thresholds.responseTime.p99}`],
    'get_messages_duration': [`p(95)<${config.thresholds.responseTime.p95}`, `p(99)<${config.thresholds.responseTime.p99}`],
    'http_req_duration': [`p(95)<${config.thresholds.responseTime.p95}`, `p(99)<${config.thresholds.responseTime.p99}`],
    'http_req_failed': [`rate<${1 - config.thresholds.successRate}`],
  },
};

export default function() {
  // اختيار مستخدمين عشوائيين
  const senderIndex = Math.floor(Math.random() * users.length);
  let receiverIndex;
  do {
    receiverIndex = Math.floor(Math.random() * users.length);
  } while (receiverIndex === senderIndex);
  
  const sender = users[senderIndex];
  const receiver = users[receiverIndex];
  
  // تسجيل دخول المرسل
  const token = login(sender.email, sender.password);
  
  if (!token) {
    failedRequests.add(1);
    return;
  }
  
  group('Messaging Flow', function() {
    // إنشاء محادثة جديدة
    group('Create Conversation', function() {
      const startTime = new Date();
      
      const conversationId = createConversation(token, [receiver.email]);
      
      const duration = new Date() - startTime;
      const success = conversationId !== null;
      
      createConversationSuccess.add(success);
      
      if (!success) {
        failedRequests.add(1);
      }
      
      // محاكاة نشاط المستخدم
      sleep(Math.random() * 2 + 1);
      
      // إذا نجح إنشاء المحادثة، أرسل رسائل
      if (conversationId) {
        // إرسال رسالة
        group('Send Message', function() {
          const messageContent = `Test message ${Math.floor(Math.random() * 1000)}`;
          
          const startTime = new Date();
          
          const messageId = sendMessage(token, conversationId, messageContent);
          
          const duration = new Date() - startTime;
          sendMessageDuration.add(duration);
          
          const success = messageId !== null;
          sendMessageSuccess.add(success);
          
          if (!success) {
            failedRequests.add(1);
          }
          
          // محاكاة نشاط المستخدم
          sleep(Math.random() * 1 + 0.5);
        });
        
        // الحصول على الرسائل
        group('Get Messages', function() {
          const startTime = new Date();
          
          const messagesRes = getMessages(token, conversationId);
          
          const duration = new Date() - startTime;
          getMessagesDuration.add(duration);
          
          const success = check(messagesRes, {
            'get messages status is 200': (r) => r.status === 200,
            'messages are returned': (r) => JSON.parse(r.body).data !== undefined,
          });
          
          getMessagesSuccess.add(success);
          
          if (!success) {
            failedRequests.add(1);
          }
          
          // محاكاة نشاط المستخدم
          sleep(Math.random() * 2 + 1);
        });
      }
    });
    
    // الحصول على قائمة المحادثات
    group('Get Conversations', function() {
      const conversationsRes = getConversations(token);
      
      check(conversationsRes, {
        'get conversations status is 200': (r) => r.status === 200,
        'conversations are returned': (r) => JSON.parse(r.body).data !== undefined,
      });
      
      // محاكاة نشاط المستخدم
      sleep(Math.random() * 2 + 1);
    });
  });
}
