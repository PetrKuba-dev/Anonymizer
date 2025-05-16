    <?php
    use PHPUnit\Framework\TestCase;

    use App\Handlers\TelHandler;
    use App\Handlers\EmailHandler;
    use App\Handlers\NameHandler;
    use App\Handlers\SurnameHandler;
    use App\Handlers\AddressHandler;
    use App\Handlers\NumberHandler;
    use App\Handlers\DateHandler;
    use App\Anonymizer;

    class HandlerTests extends TestCase
    {
        public function testTelHandler()
        {
            $handler = new TelHandler();
            $text = "Telefon: +420 123 456 789";
            $placeholder = "{_XXX_}";
            $result = $handler->handle($text, $placeholder);
            $this->assertStringNotContainsString("123 456 789", $result);
            $this->assertStringContainsString($placeholder, $result);
        }

        public function testEmailHandler()
        {
            $handler = new EmailHandler();
            $text = "Email: user@example.com";
            $placeholder = "{_XXX_}";
            $result = $handler->handle($text, $placeholder);
            $this->assertStringNotContainsString("user@example.com", $result);
            $this->assertStringContainsString($placeholder, $result);
        }

        public function testNameHandler()
        {
            $handler = new NameHandler();
            $text = "Pan Jan Novák přišel včera.";
            $placeholder = "{_XXX_}";
            $result = $handler->handle($text, $placeholder);
            $this->assertStringNotContainsString("Jan Novák", $result);
            $this->assertStringContainsString($placeholder, $result);
        }

        public function testSurnameHandler()
        {
            $handler = new SurnameHandler();
            $text = "Pan Novák je náš klient.";
            $placeholder = "{_XXX_}";
            $result = $handler->handle($text, $placeholder);
            $this->assertStringNotContainsString("Novák", $result);
            $this->assertStringContainsString($placeholder, $result);
        }

        public function testAddressHandler()
        {
            $handler = new AddressHandler();
            $text = "Bydlí na ulici Hlavní 123.";
            $placeholder = "{_XXX_}";
            $result = $handler->handle($text, $placeholder);
            $this->assertStringNotContainsString("Hlavní 123", $result);
            $this->assertStringContainsString($placeholder, $result);
        }

        public function testNumberHandler()
        {
            $handler = new NumberHandler();
            $text = "Jeho číslo je 1234567890.";
            $placeholder = "{_XXX_}";
            $result = $handler->handle($text, $placeholder);
            $this->assertStringNotContainsString("1234567890", $result);
            $this->assertStringContainsString($placeholder, $result);
        }

        public function testDateHandler()
        {
            $handler = new DateHandler();
            $text = "Datum narození je 15. 5. 2025.";
            $placeholder = "{_XXX_}";
            $result = $handler->handle($text, $placeholder);
            $this->assertStringNotContainsString("15. 5. 2025", $result);
            $this->assertStringContainsString($placeholder, $result);
        }

        public function testInvalidKeyThrowsException()
        {
            $this->expectException(InvalidArgumentException::class);
            $anonymizer = new Anonymizer();
            $anonymizer->anonymize('text', ['UNKNOWN_KEY']);
        }
    }