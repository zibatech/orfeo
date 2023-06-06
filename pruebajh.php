<script src="http://crypto-js.googlecode.com/svn/tags/3.1.2/build/rollups/aes.js"></script>
<script>
var encrypted = CryptoJS.AES.encrypt("Message", "Secret Passphrase");
// AABsAABkAABiAAAAAAAAAABNAABlAABPAAC0AABHAAA=

var decrypted = CryptoJS.AES.decrypt(encrypted, "Secret Passphrase");
// 4d657373616765
alert(decrypted);

// Message
alert(decrypted.toString(CryptoJS.enc.Utf8));
</script>
