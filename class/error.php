<?
	global $error;
	
	$error = array(
		101 => "this station does not exist", //�� ���������� ������ �������
		102 => "this station is already authorized by", //������ ������� ��� ��������������
		103 => "missing or empty hash", //����������� hash ��� ������
		201 => "wrong pair of login and password", //������������ ����� ��� ������
		202 => "the instructor is already authorized", //������ ��������� ��� �����������
		203 => "no login", //����������� �����
		204 => "no password", //����������� ������
		205 => "no user(token)", //����������� ������ ������������ �� token
		206 => "missing or empty token", //����������� ��� ������ token
		301 => "no qr code", //����������� qr code
		302 => "a child with this qr code does not exist", //������� � ������ qr ����� �� ����������
		303 => "insufficient funds", //������������ ������� � ������� ��� ����� �� �������
	);
?>