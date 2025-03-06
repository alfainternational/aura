<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Conversation;
use App\Models\ConversationParticipant;
use App\Models\Message;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MediaAndFilesTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $sender;
    protected $receiver;
    protected $conversation;

    /**
     * إعداد بيئة الاختبار
     */
    public function setUp(): void
    {
        parent::setUp();
        
        // إنشاء تخزين وهمي
        Storage::fake('public');
        
        // إنشاء مستخدمين
        $this->sender = User::factory()->create([
            'name' => 'Sender User',
            'email' => 'sender@example.com',
            'user_type' => 'user',
            'email_verified_at' => now(),
        ]);
        
        $this->receiver = User::factory()->create([
            'name' => 'Receiver User',
            'email' => 'receiver@example.com',
            'user_type' => 'user',
            'email_verified_at' => now(),
        ]);
        
        // إنشاء محادثة
        $this->conversation = Conversation::create([
            'type' => 'individual',
            'created_by' => $this->sender->id,
        ]);
        
        // إضافة المشاركين
        ConversationParticipant::create([
            'conversation_id' => $this->conversation->id,
            'user_id' => $this->sender->id,
            'is_admin' => true,
            'joined_at' => now(),
        ]);
        
        ConversationParticipant::create([
            'conversation_id' => $this->conversation->id,
            'user_id' => $this->receiver->id,
            'is_admin' => false,
            'joined_at' => now(),
        ]);
    }

    /**
     * اختبار رفع صورة ملف شخصي
     */
    public function test_upload_profile_image()
    {
        $token = $this->sender->createToken('auth_token')->plainTextToken;
        
        // إنشاء ملف وهمي
        $file = UploadedFile::fake()->image('profile.jpg');
        
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->post('/api/profile/image', [
                'image' => $file,
            ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'image_url',
            ]);
        
        // التحقق من تخزين الملف
        $path = $response->json('image_url');
        $filename = basename($path);
        Storage::disk('public')->assertExists('profile_images/' . $filename);
        
        // التحقق من تحديث المستخدم
        $this->sender->refresh();
        $this->assertNotNull($this->sender->profile_image);
    }

    /**
     * اختبار إرسال رسالة صورة
     */
    public function test_send_image_message()
    {
        // تخطي الاختبار إذا كان امتداد GD غير موجود
        if (!extension_loaded('gd')) {
            $this->markTestSkipped('GD extension is not available.');
        }
        
        $token = $this->sender->createToken('auth_token')->plainTextToken;
        
        // إنشاء ملف وهمي
        $file = UploadedFile::fake()->image('message.jpg');
        
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->post('/api/conversations/' . $this->conversation->id . '/messages', [
                'type' => 'image',
                'file' => $file,
            ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'id',
                'conversation_id',
                'sender_id',
                'type',
                'media_url',
                'created_at',
            ]);
        
        // التحقق من تخزين الملف
        $path = $response->json('media_url');
        $filename = basename($path);
        Storage::disk('public')->assertExists('message_media/' . $filename);
        
        // التحقق من إنشاء الرسالة
        $this->assertDatabaseHas('messages', [
            'conversation_id' => $this->conversation->id,
            'sender_id' => $this->sender->id,
            'type' => 'image',
        ]);
    }

    /**
     * اختبار إرسال رسالة ملف
     */
    public function test_send_file_message()
    {
        $token = $this->sender->createToken('auth_token')->plainTextToken;
        
        // إنشاء ملف وهمي
        $file = UploadedFile::fake()->create('document.pdf', 1000, 'application/pdf');
        
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->post('/api/conversations/' . $this->conversation->id . '/messages', [
                'type' => 'file',
                'file' => $file,
            ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'id',
                'conversation_id',
                'sender_id',
                'type',
                'media_url',
                'file_name',
                'file_size',
                'file_type',
                'created_at',
            ]);
        
        // التحقق من تخزين الملف
        $path = $response->json('media_url');
        $filename = basename($path);
        Storage::disk('public')->assertExists('message_files/' . $filename);
        
        // التحقق من إنشاء الرسالة
        $this->assertDatabaseHas('messages', [
            'conversation_id' => $this->conversation->id,
            'sender_id' => $this->sender->id,
            'type' => 'file',
            'file_name' => 'document.pdf',
            'file_type' => 'application/pdf',
        ]);
    }

    /**
     * اختبار تحميل وسائط الرسالة
     */
    public function test_download_message_media()
    {
        // إنشاء رسالة صورة
        $file = UploadedFile::fake()->image('test_image.jpg');
        $path = Storage::disk('public')->putFile('message_media', $file);
        
        $message = Message::create([
            'conversation_id' => $this->conversation->id,
            'sender_id' => $this->sender->id,
            'type' => 'image',
            'media_url' => $path,
        ]);
        
        $token = $this->receiver->createToken('auth_token')->plainTextToken;
        
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->get('/api/messages/' . $message->id . '/media');

        $response->assertStatus(200)
            ->assertHeader('Content-Type', 'image/jpeg');
    }

    /**
     * اختبار تحميل ملف الرسالة
     */
    public function test_download_message_file()
    {
        // إنشاء رسالة ملف
        $file = UploadedFile::fake()->create('test_document.pdf', 1000, 'application/pdf');
        $path = Storage::disk('public')->putFile('message_files', $file);
        
        $message = Message::create([
            'conversation_id' => $this->conversation->id,
            'sender_id' => $this->sender->id,
            'type' => 'file',
            'media_url' => $path,
            'file_name' => 'test_document.pdf',
            'file_size' => 1000,
            'file_type' => 'application/pdf',
        ]);
        
        $token = $this->receiver->createToken('auth_token')->plainTextToken;
        
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->get('/api/messages/' . $message->id . '/file');

        $response->assertStatus(200)
            ->assertHeader('Content-Type', 'application/pdf')
            ->assertHeader('Content-Disposition', 'attachment; filename=test_document.pdf');
    }

    /**
     * اختبار التحقق من نوع الملف المسموح به
     */
    public function test_validate_allowed_file_types()
    {
        $token = $this->sender->createToken('auth_token')->plainTextToken;
        
        // إنشاء ملف وهمي بامتداد غير مسموح به
        $file = UploadedFile::fake()->create('malicious.exe', 1000, 'application/x-msdownload');
        
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->post('/api/conversations/' . $this->conversation->id . '/messages', [
                'type' => 'file',
                'file' => $file,
            ]);

        $response->assertStatus(422);
    }

    /**
     * اختبار التحقق من حجم الملف المسموح به
     */
    public function test_validate_file_size_limit()
    {
        $token = $this->sender->createToken('auth_token')->plainTextToken;
        
        // إنشاء ملف وهمي كبير الحجم (21MB)
        $file = UploadedFile::fake()->create('large_file.pdf', 21000, 'application/pdf');
        
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->post('/api/conversations/' . $this->conversation->id . '/messages', [
                'type' => 'file',
                'file' => $file,
            ]);

        $response->assertStatus(422);
    }

    /**
     * اختبار إرسال صورة مصغرة للصورة
     */
    public function test_image_message_generates_thumbnail()
    {
        // تخطي الاختبار إذا كان امتداد GD غير موجود
        if (!extension_loaded('gd')) {
            $this->markTestSkipped('GD extension is not available.');
        }
        
        $token = $this->sender->createToken('auth_token')->plainTextToken;
        
        // إنشاء ملف وهمي
        $file = UploadedFile::fake()->image('message.jpg', 1200, 800);
        
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->post('/api/conversations/' . $this->conversation->id . '/messages', [
                'type' => 'image',
                'file' => $file,
            ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'id',
                'media_url',
                'thumbnail_url',
            ]);
        
        // التحقق من تخزين الصورة المصغرة
        $thumbnailPath = $response->json('thumbnail_url');
        $filename = basename($thumbnailPath);
        Storage::disk('public')->assertExists('message_thumbnails/' . $filename);
    }

    /**
     * اختبار حذف وسائط الرسالة عند حذف الرسالة
     */
    public function test_delete_message_removes_media()
    {
        // إنشاء رسالة صورة
        $file = UploadedFile::fake()->image('test_image.jpg');
        $path = Storage::disk('public')->putFile('message_media', $file);
        
        $message = Message::create([
            'conversation_id' => $this->conversation->id,
            'sender_id' => $this->sender->id,
            'type' => 'image',
            'media_url' => $path,
        ]);
        
        $token = $this->sender->createToken('auth_token')->plainTextToken;
        
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->delete('/api/messages/' . $message->id);

        $response->assertStatus(200);
        
        // التحقق من حذف الملف
        Storage::disk('public')->assertMissing($path);
        
        // التحقق من حذف الرسالة
        $this->assertDatabaseMissing('messages', [
            'id' => $message->id,
        ]);
    }

    /**
     * اختبار تحديث صورة الملف الشخصي
     */
    public function test_update_profile_image()
    {
        $token = $this->sender->createToken('auth_token')->plainTextToken;
        
        // رفع صورة أولى
        $file1 = UploadedFile::fake()->image('profile1.jpg');
        
        $response1 = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->post('/api/profile/image', [
                'image' => $file1,
            ]);
        
        $path1 = $response1->json('image_url');
        
        // رفع صورة ثانية
        $file2 = UploadedFile::fake()->image('profile2.jpg');
        
        $response2 = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->post('/api/profile/image', [
                'image' => $file2,
            ]);

        $response2->assertStatus(200);
        
        $path2 = $response2->json('image_url');
        
        // التحقق من حذف الصورة القديمة
        Storage::disk('public')->assertMissing($path1);
        
        // التحقق من تخزين الصورة الجديدة
        $filename2 = basename($path2);
        Storage::disk('public')->assertExists('profile_images/' . $filename2);
        
        // التحقق من تحديث المستخدم
        $this->sender->refresh();
        $this->assertEquals($path2, $this->sender->profile_image);
    }

    /**
     * اختبار حذف صورة الملف الشخصي
     */
    public function test_delete_profile_image()
    {
        $token = $this->sender->createToken('auth_token')->plainTextToken;
        
        // رفع صورة
        $file = UploadedFile::fake()->image('profile.jpg');
        
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->post('/api/profile/image', [
                'image' => $file,
            ]);
        
        $path = $response->json('image_url');
        
        // حذف الصورة
        $deleteResponse = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->delete('/api/profile/image');

        $deleteResponse->assertStatus(200);
        
        // التحقق من حذف الصورة
        Storage::disk('public')->assertMissing($path);
        
        // التحقق من تحديث المستخدم
        $this->sender->refresh();
        $this->assertNull($this->sender->profile_image);
    }

    /**
     * اختبار رفع صورة KYC
     */
    public function test_upload_kyc_document()
    {
        $token = $this->sender->createToken('auth_token')->plainTextToken;
        
        // إنشاء ملف وهمي
        $file = UploadedFile::fake()->image('id_document.jpg');
        
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->post('/api/kyc/documents', [
                'document_type' => 'id_card',
                'document' => $file,
            ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'document_url',
            ]);
        
        // التحقق من تخزين الملف
        $path = $response->json('document_url');
        $filename = basename($path);
        Storage::disk('public')->assertExists('kyc_documents/' . $filename);
        
        // التحقق من إنشاء سجل KYC
        $this->assertDatabaseHas('kyc_documents', [
            'user_id' => $this->sender->id,
            'document_type' => 'id_card',
            'document_url' => $path,
        ]);
    }

    /**
     * اختبار تحميل وسائط متعددة في رسالة واحدة
     */
    public function test_send_multiple_media_message()
    {
        // تخطي الاختبار إذا كان امتداد GD غير موجود
        if (!extension_loaded('gd')) {
            $this->markTestSkipped('GD extension is not available.');
        }
        
        $token = $this->sender->createToken('auth_token')->plainTextToken;
        
        // إنشاء ملفات وهمية
        $file1 = UploadedFile::fake()->image('image1.jpg');
        $file2 = UploadedFile::fake()->image('image2.jpg');
        
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->post('/api/conversations/' . $this->conversation->id . '/messages', [
                'type' => 'multi_image',
                'files' => [$file1, $file2],
            ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'id',
                'conversation_id',
                'sender_id',
                'type',
                'media_urls',
                'created_at',
            ]);
        
        // التحقق من تخزين الملفات
        $mediaUrls = $response->json('media_urls');
        $this->assertCount(2, $mediaUrls);
        
        foreach ($mediaUrls as $url) {
            $filename = basename($url);
            Storage::disk('public')->assertExists('message_media/' . $filename);
        }
        
        // التحقق من إنشاء الرسالة
        $this->assertDatabaseHas('messages', [
            'conversation_id' => $this->conversation->id,
            'sender_id' => $this->sender->id,
            'type' => 'multi_image',
        ]);
    }
}
