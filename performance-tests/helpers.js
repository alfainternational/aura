import { check } from 'k6';
import http from 'k6/http';
import { SharedArray } from 'k6/data';
import { config } from './config.js';

// مصفوفة لتخزين بيانات المستخدمين
export const users = new SharedArray('users', function () {
  // يمكن استبدال هذا بقراءة من ملف خارجي
  return Array(config.users.concurrentUsers.extreme).fill(0).map((_, i) => {
    return {
      email: `user${i}@example.com`,
      password: 'Password123!',
      name: `Test User ${i}`
    };
  });
});

// مصفوفة لتخزين بيانات المحادثات
export const conversations = new SharedArray('conversations', function () {
  return Array(20).fill(0).map((_, i) => {
    return {
      id: i + 1,
      title: `Conversation ${i + 1}`
    };
  });
});

// دالة لتسجيل الدخول والحصول على رمز المصادقة
export function login(email, password) {
  const loginRes = http.post(`${config.baseUrl}/login`, {
    email: email,
    password: password
  });
  
  check(loginRes, {
    'login successful': (r) => r.status === 200,
    'has token': (r) => JSON.parse(r.body).token !== undefined
  });
  
  if (loginRes.status !== 200) {
    console.error(`Login failed for ${email}: ${loginRes.status} ${loginRes.body}`);
    return null;
  }
  
  return JSON.parse(loginRes.body).token;
}

// دالة لإنشاء محادثة جديدة
export function createConversation(token, participants) {
  const headers = {
    'Authorization': `Bearer ${token}`,
    'Content-Type': 'application/json'
  };
  
  const conversationRes = http.post(`${config.baseUrl}/conversations`, JSON.stringify({
    type: 'individual',
    participants: participants
  }), { headers });
  
  check(conversationRes, {
    'conversation created': (r) => r.status === 201 || r.status === 200
  });
  
  if (conversationRes.status !== 201 && conversationRes.status !== 200) {
    console.error(`Create conversation failed: ${conversationRes.status} ${conversationRes.body}`);
    return null;
  }
  
  return JSON.parse(conversationRes.body).id;
}

// دالة لإرسال رسالة
export function sendMessage(token, conversationId, message) {
  const headers = {
    'Authorization': `Bearer ${token}`,
    'Content-Type': 'application/json'
  };
  
  const messageRes = http.post(`${config.baseUrl}/conversations/${conversationId}/messages`, JSON.stringify({
    type: 'text',
    content: message
  }), { headers });
  
  check(messageRes, {
    'message sent': (r) => r.status === 201
  });
  
  if (messageRes.status !== 201) {
    console.error(`Send message failed: ${messageRes.status} ${messageRes.body}`);
    return null;
  }
  
  return JSON.parse(messageRes.body).id;
}

// دالة للحصول على قائمة المحادثات
export function getConversations(token) {
  const headers = {
    'Authorization': `Bearer ${token}`
  };
  
  const conversationsRes = http.get(`${config.baseUrl}/conversations`, { headers });
  
  check(conversationsRes, {
    'get conversations successful': (r) => r.status === 200
  });
  
  return conversationsRes;
}

// دالة للحصول على رسائل محادثة
export function getMessages(token, conversationId) {
  const headers = {
    'Authorization': `Bearer ${token}`
  };
  
  const messagesRes = http.get(`${config.baseUrl}/conversations/${conversationId}/messages`, { headers });
  
  check(messagesRes, {
    'get messages successful': (r) => r.status === 200
  });
  
  return messagesRes;
}

// دالة لبدء مكالمة صوتية
export function startVoiceCall(token, participants) {
  const headers = {
    'Authorization': `Bearer ${token}`,
    'Content-Type': 'application/json'
  };
  
  const callRes = http.post(`${config.baseUrl}/voice-calls`, JSON.stringify({
    participants: participants
  }), { headers });
  
  check(callRes, {
    'voice call started': (r) => r.status === 201
  });
  
  if (callRes.status !== 201) {
    console.error(`Start voice call failed: ${callRes.status} ${callRes.body}`);
    return null;
  }
  
  return JSON.parse(callRes.body).id;
}

// دالة للحصول على الإشعارات
export function getNotifications(token) {
  const headers = {
    'Authorization': `Bearer ${token}`
  };
  
  const notificationsRes = http.get(`${config.baseUrl}/notifications`, { headers });
  
  check(notificationsRes, {
    'get notifications successful': (r) => r.status === 200
  });
  
  return notificationsRes;
}

// دالة لتحديث حالة الرسالة
export function updateMessageStatus(token, messageId, status) {
  const headers = {
    'Authorization': `Bearer ${token}`,
    'Content-Type': 'application/json'
  };
  
  const updateRes = http.put(`${config.baseUrl}/messages/${messageId}/status`, JSON.stringify({
    status: status
  }), { headers });
  
  check(updateRes, {
    'message status updated': (r) => r.status === 200
  });
  
  return updateRes;
}

// دالة للحصول على ملف تعريف المستخدم
export function getUserProfile(token, userId) {
  const headers = {
    'Authorization': `Bearer ${token}`
  };
  
  const profileRes = http.get(`${config.baseUrl}/users/${userId}`, { headers });
  
  check(profileRes, {
    'get user profile successful': (r) => r.status === 200
  });
  
  return profileRes;
}

// دالة لتحديث ملف تعريف المستخدم
export function updateUserProfile(token, userData) {
  const headers = {
    'Authorization': `Bearer ${token}`,
    'Content-Type': 'application/json'
  };
  
  const updateRes = http.put(`${config.baseUrl}/profile`, JSON.stringify(userData), { headers });
  
  check(updateRes, {
    'profile updated': (r) => r.status === 200
  });
  
  return updateRes;
}
