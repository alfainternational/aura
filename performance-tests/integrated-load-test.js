import { sleep } from 'k6';
import http from 'k6/http';
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
  updateMessageStatus,
  startVoiceCall,
  getNotifications
} from './helpers.js';

// تعريف المقاييس المخصصة
const scenarioSuccess = new Rate('scenario_success');
const failedRequests = new Counter('failed_requests');

// إعداد اختبار الأداء
export const options = {
  scenarios: {
    realistic_user_behavior: {
      executor: 'ramping-vus',
      startVUs: 10,
      stages: [
        { duration: '1m', target: 30 },
        { duration: '3m', target: 30 },
        { duration: '1m', target: 50 },
        { duration: '3m', target: 50 },
        { duration: '1m', target: 10 },
      ],
    },
  },
  thresholds: {
    'scenario_success': ['rate>0.90'],
    'http_req_duration': [`p(95)<${config.thresholds.responseTime.p95}`, `p(99)<${config.thresholds.responseTime.p99}`],
    'http_req_failed': [`rate<${1 - config.thresholds.successRate}`],
  },
};

export default function() {
  // اختيار مستخدم عشوائي
  const userIndex = Math.floor(Math.random() * users.length);
  const user = users[userIndex];
  
  // تسجيل الدخول
  const token = login(user.email, user.password);
  
  if (!token) {
    failedRequests.add(1);
    return;
  }
  
  // تحديد سيناريو عشوائي للمستخدم
  const scenario = Math.floor(Math.random() * 5);
  let scenarioSuccessful = true;
  
  switch (scenario) {
    case 0:
      // سيناريو المراسلة
      scenarioSuccessful = messagingScenario(token, user);
      break;
    case 1:
      // سيناريو المكالمات الصوتية
      scenarioSuccessful = voiceCallScenario(token, user);
      break;
    case 2:
      // سيناريو تصفح المحادثات والرسائل
      scenarioSuccessful = browsingScenario(token);
      break;
    case 3:
      // سيناريو الإشعارات
      scenarioSuccessful = notificationsScenario(token);
      break;
    case 4:
      // سيناريو تحديث الملف الشخصي
      scenarioSuccessful = profileScenario(token, user);
      break;
  }
  
  scenarioSuccess.add(scenarioSuccessful);
}

// سيناريو المراسلة
function messagingScenario(token, user) {
  let success = true;
  
  group('Messaging Scenario', function() {
    // اختيار مستخدم عشوائي للتواصل معه
    let receiverIndex;
    do {
      receiverIndex = Math.floor(Math.random() * users.length);
    } while (users[receiverIndex].email === user.email);
    
    const receiver = users[receiverIndex];
    
    // الحصول على قائمة المحادثات
    const conversationsRes = getConversations(token);
    
    if (conversationsRes.status !== 200) {
      failedRequests.add(1);
      success = false;
      return;
    }
    
    // إنشاء محادثة جديدة أو استخدام محادثة موجودة
    let conversationId;
    const conversationsData = JSON.parse(conversationsRes.body).data;
    
    if (conversationsData && conversationsData.length > 0 && Math.random() > 0.3) {
      // استخدام محادثة موجودة
      const randomConvIndex = Math.floor(Math.random() * conversationsData.length);
      conversationId = conversationsData[randomConvIndex].id;
    } else {
      // إنشاء محادثة جديدة
      conversationId = createConversation(token, [receiver.email]);
      
      if (!conversationId) {
        failedRequests.add(1);
        success = false;
        return;
      }
      
      // محاكاة نشاط المستخدم
      sleep(Math.random() * 2 + 1);
    }
    
    // إرسال عدة رسائل
    const numMessages = Math.floor(Math.random() * 5) + 1;
    
    for (let i = 0; i < numMessages; i++) {
      const messageContent = `Test message ${Math.floor(Math.random() * 1000)} - ${i}`;
      
      const messageId = sendMessage(token, conversationId, messageContent);
      
      if (!messageId) {
        failedRequests.add(1);
        success = false;
      }
      
      // محاكاة نشاط المستخدم
      sleep(Math.random() * 2 + 0.5);
    }
    
    // الحصول على الرسائل
    const messagesRes = getMessages(token, conversationId);
    
    if (messagesRes.status !== 200) {
      failedRequests.add(1);
      success = false;
    }
    
    // محاكاة نشاط المستخدم
    sleep(Math.random() * 3 + 1);
  });
  
  return success;
}

// سيناريو المكالمات الصوتية
function voiceCallScenario(token, user) {
  let success = true;
  
  group('Voice Call Scenario', function() {
    // اختيار مستخدم عشوائي للاتصال به
    let receiverIndex;
    do {
      receiverIndex = Math.floor(Math.random() * users.length);
    } while (users[receiverIndex].email === user.email);
    
    const receiver = users[receiverIndex];
    
    // بدء مكالمة صوتية
    const callId = startVoiceCall(token, [receiver.email]);
    
    if (!callId) {
      failedRequests.add(1);
      success = false;
      return;
    }
    
    // محاكاة مدة المكالمة
    sleep(Math.random() * 5 + 3);
    
    // إنهاء المكالمة
    const headers = {
      'Authorization': `Bearer ${token}`,
      'Content-Type': 'application/json'
    };
    
    const endRes = http.put(`${config.baseUrl}/voice-calls/${callId}/end`, {}, { headers });
    
    if (endRes.status !== 200) {
      failedRequests.add(1);
      success = false;
    }
    
    // محاكاة نشاط المستخدم
    sleep(Math.random() * 3 + 1);
  });
  
  return success;
}

// سيناريو تصفح المحادثات والرسائل
function browsingScenario(token) {
  let success = true;
  
  group('Browsing Scenario', function() {
    // الحصول على قائمة المحادثات
    const conversationsRes = getConversations(token);
    
    if (conversationsRes.status !== 200) {
      failedRequests.add(1);
      success = false;
      return;
    }
    
    const conversationsData = JSON.parse(conversationsRes.body).data;
    
    // محاكاة نشاط المستخدم
    sleep(Math.random() * 3 + 1);
    
    // تصفح الرسائل في عدة محادثات
    if (conversationsData && conversationsData.length > 0) {
      const numConversationsToView = Math.min(3, conversationsData.length);
      
      for (let i = 0; i < numConversationsToView; i++) {
        const conversationId = conversationsData[i].id;
        
        const messagesRes = getMessages(token, conversationId);
        
        if (messagesRes.status !== 200) {
          failedRequests.add(1);
          success = false;
        }
        
        // محاكاة نشاط المستخدم
        sleep(Math.random() * 5 + 2);
        
        // تحديث حالة الرسائل
        const messagesData = JSON.parse(messagesRes.body).data;
        
        if (messagesData && messagesData.length > 0) {
          const randomMessageIndex = Math.floor(Math.random() * messagesData.length);
          const messageId = messagesData[randomMessageIndex].id;
          
          const updateRes = updateMessageStatus(token, messageId, 'read');
          
          if (updateRes.status !== 200) {
            failedRequests.add(1);
            success = false;
          }
        }
        
        // محاكاة نشاط المستخدم
        sleep(Math.random() * 3 + 1);
      }
    }
  });
  
  return success;
}

// سيناريو الإشعارات
function notificationsScenario(token) {
  let success = true;
  
  group('Notifications Scenario', function() {
    // الحصول على الإشعارات
    const notificationsRes = getNotifications(token);
    
    if (notificationsRes.status !== 200) {
      failedRequests.add(1);
      success = false;
      return;
    }
    
    // محاكاة نشاط المستخدم
    sleep(Math.random() * 2 + 1);
    
    // تحديث حالة الإشعارات
    const notificationsData = JSON.parse(notificationsRes.body).data;
    
    if (notificationsData && notificationsData.length > 0) {
      const randomNotificationIndex = Math.floor(Math.random() * notificationsData.length);
      const notificationId = notificationsData[randomNotificationIndex].id;
      
      const headers = {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json'
      };
      
      const markReadRes = http.put(`${config.baseUrl}/notifications/${notificationId}/read`, {}, { headers });
      
      if (markReadRes.status !== 200) {
        failedRequests.add(1);
        success = false;
      }
    }
    
    // محاكاة نشاط المستخدم
    sleep(Math.random() * 3 + 1);
  });
  
  return success;
}

// سيناريو تحديث الملف الشخصي
function profileScenario(token, user) {
  let success = true;
  
  group('Profile Scenario', function() {
    // الحصول على الملف الشخصي
    const headers = {
      'Authorization': `Bearer ${token}`
    };
    
    const profileRes = http.get(`${config.baseUrl}/profile`, { headers });
    
    if (profileRes.status !== 200) {
      failedRequests.add(1);
      success = false;
      return;
    }
    
    // محاكاة نشاط المستخدم
    sleep(Math.random() * 3 + 1);
    
    // تحديث الملف الشخصي
    const updateHeaders = {
      'Authorization': `Bearer ${token}`,
      'Content-Type': 'application/json'
    };
    
    const updateData = {
      name: `${user.name} (Updated)`,
      bio: `This is a test bio for ${user.name}`,
      city: 'Khartoum'
    };
    
    const updateRes = http.put(`${config.baseUrl}/profile`, JSON.stringify(updateData), { headers: updateHeaders });
    
    if (updateRes.status !== 200) {
      failedRequests.add(1);
      success = false;
    }
    
    // محاكاة نشاط المستخدم
    sleep(Math.random() * 3 + 1);
  });
  
  return success;
}
