<?
	global $error;
	
	$error = array(
		101 => "this station does not exist", //�� ���������� ������ �������
		102 => "this station is already authorized by", //������ ������� ��� ��������������
		103 => "missing or empty hash", //����������� hash ��� ������
		150 => "id the city does not exist or is empty", //id ������ �� ���������� ��� ������
		201 => "wrong pair of login and password", //������������ ����� ��� ������
		202 => "the instructor is already authorized", //������ ��������� ��� �����������
		203 => "no login", //����������� �����
		204 => "no password", //����������� ������
		205 => "no user(token)", //����������� ������ ������������ �� token
		206 => "missing or empty token", //����������� ��� ������ token
		207 => "id of the mentor does not exist or is empty", //������� id ���������� �� ���������� ��� ������
		208 => "the custom is already authorized", //������ ���������� ��� �����������
		209 => "id of the custom does not exist or is empty", //������� id ����������� �� ���������� ��� ������
		210 => "Users of this type does not exist", //������� ���� ������������ �� ����������
		301 => "no qr code", //����������� qr code
		302 => "a child with this qr code does not exist", //������� � ������ qr ����� �� ����������
		303 => "insufficient funds", //������������ ������� � ������� ��� ����� �� �������
		304 => "no child(id)", //������� ������� �� ���������� �� id
		305 => "the child already at the station", //������� ��� �� �������
		307 => "the child is not in the city", //������� ������� ��� � ������
		306 => "bug database (more than one records of the child in the city)", //������ ���� (������ ����� ������ � ������� � ������)
		401 => "missing or empty reg_id", //����������� reg_id ��� ������
		402 => "Let one of the parameters"//���� ���� �� ����������
	);
?>