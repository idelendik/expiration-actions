import {PanelRow, TextControl, DateTimePicker, ToggleControl} from "@wordpress/components";
import {PluginDocumentSettingPanel} from "@wordpress/editor";
import {useSelect, useDispatch} from "@wordpress/data";
import {useState} from "@wordpress/element";
import {__} from "@wordpress/i18n";
import {registerPlugin} from "@wordpress/plugins";

const ExpirationActionsGutenbergMetaProps = () => {
    const {fieldNames} = expirationActionsMetaFieldsData;

    // IMPORTANT: Change these keys ORDER with caution,
    // as it depends on fields order within PostMeta PHP class
    const [
        IS_META_BOX_DISABLED_KEY,
        EXPIRATION_DATETIME_KEY,
        REDIRECT_URL_KEY
    ] = fieldNames;

    const boolToInt = function (value) {
        return value ? '1' : '0';
    };

    const intToBool = function (value) {
        return '0' === value ? false : true;
    }

    const meta = useSelect(select => (
        select('core/editor').getEditedPostAttribute('meta')
    ), []);

    const {editPost} = useDispatch('core/editor');

    const setMeta = keyAndValue => {
        editPost({meta: keyAndValue})
    }

    const [isDisabled, setIsDisabled] = useState(intToBool(meta[IS_META_BOX_DISABLED_KEY]));

    return (
        <PluginDocumentSettingPanel
            name="expiration-actions"
            title={__('Expiration Actions', 'expiration-actions')}
        >
            <PanelRow>
                <ToggleControl
                    label={isDisabled ? __('Enable', 'expiration-actions') : __('Disable', 'expiration-actions')}
                    checked={isDisabled}
                    onChange={newValue => {
                        setMeta({[IS_META_BOX_DISABLED_KEY]: boolToInt(newValue)});
                        setIsDisabled(newValue);
                    }}
                />
            </PanelRow>

            {!isDisabled && (
                <>
                    <PanelRow>
                        <DateTimePicker
                            currentDate={meta[EXPIRATION_DATETIME_KEY]}
                            onChange={newValue => setMeta({[EXPIRATION_DATETIME_KEY]: newValue})}
                        />
                    </PanelRow>

                    <PanelRow>
                        <TextControl
                            label={__('Redirect URL', 'expiration-actions')}
                            onChange={newValue => setMeta({[REDIRECT_URL_KEY]: newValue})}
                            value={meta[REDIRECT_URL_KEY]}
                        />
                    </PanelRow>
                </>
            )}
        </PluginDocumentSettingPanel>
    )
}

registerPlugin('expiration-actions-gutenberg-meta-props', {
    render: ExpirationActionsGutenbergMetaProps,
});