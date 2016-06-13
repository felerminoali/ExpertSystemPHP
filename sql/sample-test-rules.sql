
#-- delete from rule;

#------------ Simple inference test ----------------------------
insert into rule (ruleset, conditionString, actionString) values ("test", "$x > 1", "$y = 10;");
insert into rule (ruleset, conditionString, actionString) values ("test", "$y > 1", "$z = 100;");
insert into rule (ruleset, conditionString, actionString) values ("test", "$z > 1", "$a = 1;");


#------------- To find the sound of an animal ------------------
insert into rule (ruleset, conditionString, actionString) values ("animal", "$animal == 'bee'", "$sound = 'buzz';");
insert into rule (ruleset, conditionString, actionString) values ("animal", "$animal == 'bird'", "$sound = 'tweet';");
insert into rule (ruleset, conditionString, actionString) values ("animal", "$animal == 'hen'", "$sound = 'cluck';");
insert into rule (ruleset, conditionString, actionString) values ("animal", "$animal == 'rat'", "$sound = 'squeak';");

insert into rule (ruleset, conditionString, actionString) values ("animal", "$fly == true && $size == 'small'", "$animal = 'bee';");
insert into rule (ruleset, conditionString, actionString) values ("animal", "$fly == true && $size == 'large' && $legs == 2", "$animal = 'bird';");
insert into rule (ruleset, conditionString, actionString) values ("animal", "$legs == 4", "$animal = 'rat'; $fly = false;");
insert into rule (ruleset, conditionString, actionString) values ("animal", "$fly == false && $legs == 2", "$animal = 'hen';");


#------------- Suggest a degree to be followed by a student based on completed external courses ------------------
insert into rule (ruleset, priority, conditionString, actionString) values ("degree", 1, "$external_course_followed_x1 == true", "$score['X'] += 1;");
insert into rule (ruleset, priority, conditionString, actionString) values ("degree", 1, "$external_course_followed_x2 == true", "$score['X'] += 1; $score['Y'] += 1;");
insert into rule (ruleset, priority, conditionString, actionString) values ("degree", 1, "$external_course_followed_y1 == true", "$score['Y'] += 2;");
insert into rule (ruleset, priority, conditionString, actionString) values ("degree", 1, "$external_course_followed_y2 == true", "$score['Y'] += 0.1;");
insert into rule (ruleset, priority, conditionString, actionString) values ("degree", 0, "isset($score)", "arsort($score); $follow = key($score);");


#------------- This is a more complicated sample --------------------
#------------- Suggest a degree to be follwed by a student based on completed courses (do wildcard matching) -------------------

#-- Define a function to load excempted subjects for a given external course
#-- This may not be kept as a rule in the database. This is not a rule.
insert into rule (ruleset, priority, conditionString, actionString) values (
"degree2", 2,
	'true',
		'function get_exempted_subject_codes($externalCourseCode) { 
			if ($externalCourseCode == "x1") return array("ecx4230" => "ecx4230", "ecx4531" => "ecx4531", "ecx4932" => "ecx4932");
			if ($externalCourseCode == "x2") return array("bcx4230" => "bcx4230", "bcx4431" => "bcx4431");
			if ($externalCourseCode == "y1") return array("ccx4230" => "ccx4230", "ccx4831" => "ccx4831", "ccx4432" => "ccx4432");
			if ($externalCourseCode == "y2") return array("dcx4230" => "dcx4230");
		 }');

#-- Define a function to load details of a subject given the subject code.
#-- This may not be kept as a rule in the database. This is not a rule.
insert into rule (ruleset, priority, conditionString, actionString) values (
"degree2", 2,
	'true',
		'function load_subject_details($subjectCode) {
			$credits = substr($subjectCode, 4, 1);
			return array("id" => $subjectCode, "credits" => $credits); 
		}');

#-- Define a function to determine if a given subject is core for a field. For testing purposes, we determine randomly. 
#-- This may not be kept as a rule in the database. This is not a rule.
insert into rule (ruleset, priority, conditionString, actionString) values (
"degree2", 2,
	'true',
		'function is_core_subject_for_field($subjectCode, $fieldCode) {
			if (rand(0, 1) == 0) {
				return true;
			}
			return false; 
		}');																								

#-- Define the fields available for students. Simple facts!
insert into rule (ruleset, priority, conditionString, actionString) values (
"degree2", 2,
	'true',
		'$field_electrical_available = "electrical";
		 $field_electronic_available = "electronic";
		 $field_civil_available = "civil";
		 $field_computer_available = "computer";');


		 
#---------  Following are the rules that represent actual knowledge ----------------


# Find the exempted subjects (get their code)														
insert into rule (ruleset, priority, conditionString, actionString) values (
"degree2", 1, 
	'$external_course_followed_WILDCARD1 == true',
		'extract(get_exempted_subject_codes("WILDCARD1"), EXTR_PREFIX_ALL, "subject_exempted");');

# Load the details of exempted subjects
insert into rule (ruleset, priority, conditionString, actionString) values (
"degree2", 1,
	'isset($subject_exempted_WILDCARD1)',
		'$subject_WILDCARD1 = load_subject_details("WILDCARD1");');
											

# Rank the fields (Add the subject credits to fields)
insert into rule (ruleset, priority, conditionString, actionString) values (
"degree2", 0,
	'is_core_subject_for_field($subject_exempted_WILDCARD1, $field_WILDCARD2_available)',
		'$fieldRank["WILDCARD2"] += $subject_WILDCARD1["credits"];');

# Get the highest ranked field
insert into rule (ruleset, priority, conditionString, actionString) values (
"degree2", -1,
	'isset($fieldRank)',
		'arsort($fieldRank);
         $follow = key($fieldRank);');
