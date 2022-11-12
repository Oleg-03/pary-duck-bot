const burger_button = document.querySelector(".burger_button");
const burger = document.querySelector(".burger");
const grup = document.querySelector(".grup");
const grup_title_container = document.querySelector(".grup_title_container");

const create_kurs_button = document.createElement("button");
const create_prof_button = document.createElement("button");
const grup_container = document.createElement("div");
const main_block =  document.createElement("div");
const new_window = document.createElement("div");
const title = document.createElement("h2");
const form = document.createElement("form");
const br_1 = document.createElement("br");
const br_2 = document.createElement("br");
const br_3 = document.createElement("br");
const select_1 = document.createElement("select");
const select_2 = document.createElement("select");
const select_3 = document.createElement("select");
const label_container = document.createElement("div")
const input_container = document.createElement("div")
const label_name = document.createElement("label");
const label_teacher = document.createElement("label");
const label_office = document.createElement("label");
const label_link = document.createElement("label");
const input_name = document.createElement("input");
const input_4 = document.createElement("input");
const input_date = document.createElement("input");
const input_teacher = document.createElement("input");
const input_office = document.createElement("input");
const input_link = document.createElement("input");
const button_cancel = document.createElement("button");
const button_save = document.createElement("button");

burger_button.addEventListener("click", function(event){
    event.preventDefault;

    if(document.querySelector(".active")==null){

        burger.classList.add("active");
        grup.classList.add("grup_active");

        const grup_title = document.createElement("h2");

        grup_title.className = "grup_title";
        create_kurs_button.className = "create_kurs_button";

        create_kurs_button.textContent = "Додати курс +";
        grup_title.textContent = "Групи";

        $.ajax({
            url: 'http://localhost/web/json_data_storage/TreeStorage.class.php',
            method: 'post',
            dataType: 'html',
            success: function(data){
                const info = JSON.parse(data);
                /*const info_length = info.length - 1;
                for(let i=0; i<=info_length; i++){
                    const kurs_spun = document.createElement("spun");
                    kurs_spun.className = "kurs_spun";
                    kurs_spun.textContent = info[i];
                    grup_container.appendChild(kurs_spun);
                }*/
                console.log(info);
            }
        });

        grup_title_container.appendChild(grup_title);
        grup.appendChild(grup_container);
        grup_container.appendChild(create_kurs_button);

        

        create_kurs_button.addEventListener("click", function(){
            main_block.className = "main_block";
            new_window.className = "grup_window";
            title.className = "change_title";
            form.className = "change_form";
            input_name.className = "grup_input";
            button_cancel.className = "change_button_cancel";
            button_save.className = "change_button_save";

            title.textContent = "Назва курсу:";
            button_cancel.textContent = "Скасувати";
            button_save.textContent = "Зберегти";

            main_block.appendChild(new_window);
            new_window.appendChild(title);
            new_window.appendChild(form);
            form.appendChild(input_name);
            new_window.appendChild(button_cancel);
            new_window.appendChild(button_save);

            document.querySelector("body").appendChild(main_block);

            button_cancel.addEventListener("click", function(){
                main_block.remove();
                new_window.remove();
                title.remove();
                form.remove();
                input_name.remove();
                button_cancel.remove();
                button_save.remove();
            });
            button_save.addEventListener("click", function(){
                if(input_name.value != ""){
                    const kurs_spun = document.createElement("spun");
                    kurs_spun.className = "kurs_spun";
                    kurs_spun.textContent = input_name.value;
                    grup_container.appendChild(kurs_spun);

                    $.ajax({
                        url: 'http://localhost/web/main.php',
                        method: 'post',
                        dataType: 'html',
                        data: {
                            "indecator": "add",
                            "add": "kurs", 
                            "kurs": input_name.value
                        }, 
                        success: function(data){
                            console.log(data);
                        }
                    });

                    main_block.remove();
                    new_window.remove();
                    title.remove();
                    form.remove();
                    input_name.remove();
                    button_cancel.remove();
                    button_save.remove();
                }
            });
        });
    }
    else{
            burger.classList.remove("active");
            grup.classList.remove("grup_active");
            document.querySelector(".grup_title").remove();
    }
});


const create = document.querySelector(".create");

/*create.addEventListener("click", function(){
    main_block.className = "main_block";
    new_window.className = "add_window";
    form.className = "add_form"
    label_container.className = "label_container";
    label_name.className = "add_label";
    label_teacher.className = "add_label";
    label_office.className = "add_label";
    label_link.className = "add_label";
    input_name.className = "add_input";
    input_teacher.className = "add_input";
    input_office.className = "add_input";
    input_link.className = "add_input";
    button_cancel.className = "button_cancel";
    button_save.className = "button_save";


    main_block.appendChild(new_window);
    new_window.appendChild(form);
    label_container.appendChild(label_name);
    label_container.appendChild(br_1);
    label_container.appendChild(label_teacher);
    label_container.appendChild(br_2);
    label_container.appendChild(label_office);
    label_container.appendChild(br_3);
    input_container.appendChild(input_link);
    input_container.appendChild(input_name);
    input_container.appendChild(input_teacher);
    input_container.appendChild(input_office);
    label_container.appendChild(label_link);
    
    form.appendChild(label_container);
    form.appendChild(input_container);
    new_window.appendChild(button_cancel);
    new_window.appendChild(button_save);

    label_name.textContent = "Назва пари:";
    label_teacher.textContent = "Викладач:";
    label_office.textContent = "Кабінет:";
    label_link.textContent = "Посилання:";
    button_cancel.textContent = "Скасувати";
    button_save.textContent = "Зберегти";

    document.querySelector("body").appendChild(main_block);

    button_cancel.addEventListener("click", function(){
        main_block.remove();
    });
});*/

create.addEventListener("click", function(){
    main_block.className = "main_block";
    new_window.className = "add_window";
    title.className = "advertisemen_title";
    form.className = "advertisement_form";
    input_container.className = "advertisement_setting_container";
    label_teacher.className = "advertisement_label";
    select_1.className = "advertisement_select";
    select_2.className = "advertisement_select";
    select_3.className = "advertisement_select";
    input_name.className = "advertisement_input";
    input_teacher.className = "radio_button";
    input_office.className = "radio_button";
    input_link.className = "radio_button";
    input_4.className = "radio_button";
    button_cancel.className = "button_cancel";
    button_save.className = "button_save";

    select_1.setAttribute("value", "Курс");
    input_teacher.setAttribute("type", "radio");
    input_office.setAttribute("type", "radio");
    input_link.setAttribute("type", "radio");
    input_4.setAttribute("type", "radio");

    main_block.appendChild(new_window);
    new_window.appendChild(title);
    new_window.appendChild(form);
    form.appendChild(input_container);
    input_container.appendChild(input_teacher);
    input_container.appendChild(label_teacher);
    input_container.appendChild(input_office);
    input_container.appendChild(select_1);
    input_container.appendChild(input_link);
    input_container.appendChild(select_2);
    input_container.appendChild(input_4);
    input_container.appendChild(select_3);
    form.appendChild(input_name);

    new_window.appendChild(button_cancel);
    new_window.appendChild(button_save);

    title.textContent = "Оголошення:";
    label_teacher.textContent = "Всі";
    button_cancel.textContent = "Скасувати";
    button_save.textContent = "Зберегти";

    document.querySelector("body").appendChild(main_block);

    button_cancel.addEventListener("click", function(){
        main_block.remove();
    });
});

document.querySelector(".change").addEventListener("click", function(){
    main_block.className = "main_block";
    new_window.className = "change_window";
    title.className = "change_title";
    form.className = "change_form";
    input_container.className = "advertisement_setting_container";
    label_teacher.className = "change_label";
    label_office.className = "change_label";
    label_link.className = "change_label";
    input_teacher.className = "change_radio_button";
    input_office.className = "change_radio_button";
    input_link.className = "change_radio_button";
    button_cancel.className = "change_button_cancel";
    button_save.className = "change_button_save";
    button_save.id = "next";

    main_block.appendChild(new_window);
    new_window.appendChild(title);
    new_window.appendChild(form);
    form.appendChild(input_teacher);
    form.appendChild(label_teacher);
    form.appendChild(br_1);
    form.appendChild(input_office);
    form.appendChild(label_office);
    form.appendChild(br_2);
    form.appendChild(input_link);
    form.appendChild(label_link);
    new_window.appendChild(button_cancel);
    new_window.appendChild(button_save);

    input_teacher.setAttribute("type", "radio");
    input_office.setAttribute("type", "radio");
    input_link.setAttribute("type", "radio");

    title.textContent = "Тип зміни:";
    label_teacher.textContent = "Заміна в розкладі";
    label_office.textContent = "Скорочені пари";
    label_link.textContent = "Не буде пари";
    button_cancel.textContent = "Скасувати";
    button_save.textContent = "Далі";

    document.querySelector("body").appendChild(main_block);

    document.querySelector("#next").addEventListener("click", function(){
        button_save.id = "next_2";
        title.textContent = "Період:";
        label_teacher.textContent = "Одна пара";
        label_office.textContent = "Один день";
        label_link.textContent = "Період терміну";

        document.querySelector("#next_2").addEventListener("click", function(){
            input_teacher.remove();
            br_1.remove();
            input_office.remove();
            label_office.remove();
            br_2.remove();
            input_link.remove();
            label_link.remove();

            label_teacher.textContent = "Дата";
            form.appendChild(input_date);
            form.appendChild(br_1);
            form.appendChild(select_1);

            input_date.setAttribute("type", "date");
        });
    });
});
